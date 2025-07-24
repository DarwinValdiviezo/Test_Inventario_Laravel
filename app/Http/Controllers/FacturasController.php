<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturaRequest;
use App\Models\Auditoria;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;

/**
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\FacturaDetalle[] $detalles
 * @property \App\Models\Cliente|null $cliente
 * @property \App\Models\User|null $user
 */
class FacturasController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Factura::class);
        
        $facturas = Factura::query();
        
        if ($request->has('eliminadas')) {
            $facturas = $facturas->onlyTrashed();
            
            // Obtener logs de auditoría para facturas eliminadas con filtros
            $logs = Auditoria::where('model_type', Factura::class)
                ->whereIn('action', ['delete', 'restore', 'forceDelete'])
                ->with('user');
            
            // Filtros para la tabla de auditoría
            if ($request->filled('log_buscar')) {
                $logBuscar = $request->input('log_buscar');
                $logs = $logs->where(function($q) use ($logBuscar) {
                    $q->where('description', 'like', "%$logBuscar%")
                      ->orWhere('observacion', 'like', "%$logBuscar%")
                      ->orWhereHas('user', function($userQuery) use ($logBuscar) {
                          $userQuery->where('name', 'like', "%$logBuscar%");
                      });
                });
            }
            
            if ($request->filled('log_accion')) {
                $logs = $logs->where('action', $request->input('log_accion'));
            }
            
            if ($request->filled('log_usuario')) {
                $logs = $logs->whereHas('user', function($q) {
                    $q->where('id', $request->input('log_usuario'));
                });
            }
            
            if ($request->filled('log_fecha_desde')) {
                $logs = $logs->whereDate('created_at', '>=', $request->input('log_fecha_desde'));
            }
            
            if ($request->filled('log_fecha_hasta')) {
                $logs = $logs->whereDate('created_at', '<=', $request->input('log_fecha_hasta'));
            }
            
            $logs = $logs->orderBy('created_at', 'desc')
                        ->paginate($request->input('log_per_page', 10))
                        ->withQueryString();
            
            // Obtener usuarios para el filtro
            $usuarios = \App\Models\User::orderBy('name')->get();
        } else {
            $logs = collect();
            $usuarios = collect();
        }
        
        // Filtros de búsqueda y estado para facturas
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $facturas = $facturas->whereHas('cliente', function($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%");
            })->orWhere('id', 'like', "%$buscar%");
        }
        
        if ($request->filled('estado')) {
            $facturas = $facturas->where('estado', $request->input('estado'));
        }
        
        if ($request->filled('cliente_id')) {
            $facturas = $facturas->where('cliente_id', $request->input('cliente_id'));
        }
        
        $facturas = $facturas->with(['cliente', 'usuario', 'detalles.producto'])
                            ->orderBy('id', 'desc')
                            ->paginate($request->input('per_page', 10))
                            ->withQueryString();
        
        // Obtener clientes para el filtro
        $clientes = Cliente::orderBy('nombre')->get();
        
        return view('facturas.index', compact('facturas', 'logs', 'usuarios', 'clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Factura::class);
        
        $clientes = Cliente::where('estado', 'activo')->orderBy('nombre')->get();
        $productos = Producto::where('stock', '>', 0)->orderBy('nombre')->get();
        
        return view('facturas.create', compact('clientes', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacturaRequest $request)
    {
        $this->authorize('create', Factura::class);
        
        $data = $request->validated();
        $data['usuario_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $subtotal = 0;
        
        DB::beginTransaction();
        try {
            // Validar stock antes de crear la factura
            foreach ($data['productos'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception('Stock insuficiente para el producto: ' . $producto->nombre . ' - Stock disponible: ' . $producto->stock . ' - Cantidad solicitada: ' . $item['cantidad']);
                }
            }
            
            // Calcular subtotal
            foreach ($data['productos'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $subtotal += $producto->precio * $item['cantidad'];
            }
            
            // Calcular IVA y total
            $iva = $subtotal * 0.15; // IVA 15%
            $total = $subtotal + $iva;
            
            // Crear factura con datos SRI
            $factura = Factura::create([
                'cliente_id' => $data['cliente_id'],
                'usuario_id' => $data['usuario_id'],
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total,
                'estado' => 'activa',
                'created_by' => $data['created_by'],
                'forma_pago' => $data['forma_pago'] ?? 'EFECTIVO',
                'estado_firma' => 'PENDIENTE',
                'estado_emision' => 'PENDIENTE',
            ]);
            
            // Generar datos SRI automáticamente
            $factura->generarDatosSRI();
            
            // Crear detalles y actualizar stock
            $detalles = [];
            foreach ($data['productos'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                
                // Actualizar stock
                $stockAnterior = $producto->stock;
                $producto->stock -= $item['cantidad'];
                $producto->save();
                
                \Log::info("Stock actualizado para producto {$producto->nombre}: {$stockAnterior} -> {$producto->stock} (cantidad vendida: {$item['cantidad']})");
                
                $subtotalDetalle = $producto->precio * $item['cantidad'];
                
                $detalles[] = FacturaDetalle::create([
                    'factura_id' => $factura->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotalDetalle,
                    'created_by' => Auth::id(),
                ]);
            }
            
            // Generar firma digital y QR
            $factura->generarFirmaYQR();
            
            $this->registrarAuditoria('create', $factura, null, $factura->toArray(), 'Factura creada con datos SRI y stock actualizado');
            
            DB::commit();
            
            return redirect()->route('facturas.show', $factura)
                           ->with('success', 'Factura #' . $factura->getNumeroFormateado() . ' creada exitosamente por $' . number_format($factura->total, 2) . ' con autorización SRI: ' . $factura->getEstadoAutorizacion());
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al crear factura: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $factura = Factura::withTrashed()->with(['cliente', 'usuario', 'detalles.producto'])->findOrFail($id);
        $this->authorize('view', $factura);
        
        return view('facturas.show', compact('factura'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factura $factura)
    {
        $this->authorize('update', $factura);
        
        $clientes = Cliente::where('estado', 'activo')->orderBy('nombre')->get();
        $productos = Producto::where('stock', '>', 0)->orderBy('nombre')->get();
        
        return view('facturas.edit', compact('factura', 'clientes', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Este método ahora solo maneja la vista de gestión
        // No se realizan actualizaciones en la base de datos
        return redirect()->route('facturas.edit', $id)
                       ->with('info', 'Esta es una vista de gestión. Las facturas no se pueden editar por razones contables.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $factura = Factura::findOrFail($id);
            $this->authorize('delete', $factura);
            
            // Validación manual
            if (empty($request->password)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'La contraseña es requerida.');
            }
            
            if (empty($request->observacion)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'La observación es requerida.');
            }
            
            if (strlen(trim($request->observacion)) < 10) {
                DB::rollBack();
                return redirect()->back()->with('error', 'La observación debe tener al menos 10 caracteres.');
            }

            // Verificar contraseña de administrador
            if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Contraseña incorrecta.');
            }
            
            $old = $factura->toArray();
            
            // Reversar stock al anular
            foreach ($factura->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $stockAnterior = $producto->stock;
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                    
                    \Log::info("Stock revertido para producto {$producto->nombre}: {$stockAnterior} -> {$producto->stock} (cantidad devuelta: {$detalle->cantidad})");
                }
            }
            
            $factura->delete();
            
            $this->registrarAuditoria('delete', $factura, $old, null, 'Factura anulada (soft) y stock revertido', $request->observacion);
            
            DB::commit();
            
            return redirect()->route('facturas.index')
                           ->with('success', 'Factura #' . $factura->id . ' anulada exitosamente y stock revertido por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al eliminar factura: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar la factura: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);

            // Verificar contraseña de administrador
            if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Contraseña incorrecta.');
            }

            $factura = Factura::onlyTrashed()->findOrFail($id);
            $this->authorize('restore', $factura);
            
            // Actualizar stock al desanular (restaurar)
            foreach ($factura->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    // Verificar que hay suficiente stock disponible
                    if ($producto->stock < $detalle->cantidad) {
                        throw new \Exception('Stock insuficiente para restaurar la factura. Producto: ' . $producto->nombre . ' - Stock disponible: ' . $producto->stock . ' - Cantidad requerida: ' . $detalle->cantidad);
                    }
                    $stockAnterior = $producto->stock;
                    $producto->stock -= $detalle->cantidad;
                    $producto->save();
                    
                    \Log::info("Stock actualizado al restaurar factura para producto {$producto->nombre}: {$stockAnterior} -> {$producto->stock} (cantidad vendida: {$detalle->cantidad})");
                }
            }
            
            $factura->restore();
            
            $this->registrarAuditoria('restore', $factura, null, $factura->toArray(), 'Factura restaurada y stock actualizado', $request->observacion);
            
            DB::commit();
            
            return redirect()->route('facturas.index', ['eliminadas' => 1])
                           ->with('success', 'Factura #' . $factura->id . ' restaurada exitosamente y stock actualizado por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al restaurar factura: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al restaurar la factura: ' . $e->getMessage());
        }
    }

    public function forceDelete(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);

            // Verificar contraseña de administrador
            if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Contraseña incorrecta.');
            }

            $factura = Factura::onlyTrashed()->findOrFail($id);
            $this->authorize('forceDelete', $factura);
            
            $old = $factura->toArray();
            
            // Eliminar detalles asociados permanentemente
            FacturaDetalle::where('factura_id', $factura->id)->forceDelete();
            $factura->forceDelete();
            
            $this->registrarAuditoria('forceDelete', $factura, $old, null, 'Factura eliminada permanentemente', $request->observacion);
            
            DB::commit();
            
            return redirect()->route('facturas.index', ['eliminadas' => 1])
                           ->with('success', 'Factura #' . $factura->id . ' eliminada permanentemente por ' . auth()->user()->name . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error al eliminar permanentemente factura: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar permanentemente la factura: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for the specified factura.
     */
    public function generatePDF($id)
    {
        $factura = Factura::withTrashed()->with(['cliente', 'usuario', 'detalles.producto'])->findOrFail($id);
        $this->authorize('view', $factura);
        
        $pdf = PDF::loadView('facturas.pdf', compact('factura'));
        
        return $pdf->download('factura-' . $factura->id . '.pdf');
    }

    /**
     * Download PDF for the specified factura (alias for generatePDF).
     */
    public function downloadPDF($id)
    {
        return $this->generatePDF($id);
    }

    /**
     * Generar PDF de vista previa antes de guardar la factura
     */
    public function previewPDF(Request $request)
    {
        try {
            $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
            ]);

            $cliente = Cliente::findOrFail($request->cliente_id);
            $productos = [];
            $total = 0;

            foreach ($request->productos as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $cantidad = $item['cantidad'];
                $subtotal = $producto->precio * $cantidad;
                $total += $subtotal;

                $productos[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio' => $producto->precio,
                    'subtotal' => $subtotal,
                ];
            }

            // Generar PDF usando la misma vista que las facturas reales
            $pdf = PDF::loadView('facturas.pdf', [
                'factura' => (object) [
                    'id' => 'PREVIEW',
                    'cliente' => $cliente,
                    'productos' => $productos,
                    'total' => $total,
                    'created_at' => now(),
                    'estado' => 'activa',
                ],
                'esPreview' => true
            ]);

            return $pdf->download('factura-preview.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF de vista previa: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar el PDF'], 500);
        }
    }

    /**
     * Send factura by email.
     */
    public function sendEmail(Request $request, $id)
    {
        \Log::info('Método sendEmail llamado', [
            'request_method' => $request->method(),
            'request_url' => $request->url(),
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'No autenticado'
        ]);
        
        try {
            $factura = Factura::withTrashed()->with(['cliente', 'usuario', 'detalles.producto'])->findOrFail($id);
            \Log::info('Factura encontrada', ['factura_id' => $factura->id]);
            
            $this->authorize('view', $factura);
            \Log::info('Autorización pasada');
            
            $request->validate([
                'email' => 'required|email',
                'asunto' => 'required|string|max:255',
                'mensaje' => 'nullable|string|max:500',
            ]);
            \Log::info('Validación pasada');
            
            \Log::info('Iniciando envío de factura por email (API SendGrid)', [
                'factura_id' => $factura->id,
                'email_destino' => $request->email,
                'usuario' => auth()->user()->name,
            ]);
            
            // Usar el servicio EmailService (API SendGrid directa)
            $emailService = new \App\Services\EmailService();
            $resultado = $emailService->enviarFactura(
                $factura,
                $request->email,
                $request->asunto,
                $request->mensaje ?? ''
            );
            
            if ($resultado) {
                \Log::info('Email enviado exitosamente (API SendGrid)');
                return redirect()->route('facturas.show', $factura)
                               ->with('success', 'Factura enviada exitosamente a ' . $request->email);
            } else {
                \Log::error('Error al enviar email (API SendGrid)');
                return redirect()->back()->with('error', 'Error al enviar la factura por email. Revisa los logs para más detalles.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación al enviar factura por email: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            \Log::error('Error de autorización al enviar factura por email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'No tienes permisos para enviar esta factura.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Factura no encontrada al enviar email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Factura no encontrada.');
        } catch (\Exception $e) {
            \Log::error('Error al enviar factura por email (API SendGrid): ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al enviar la factura por email: ' . $e->getMessage());
        }
    }

    /**
     * Método temporal para debuggear el stock de productos
     */
    public function debugStock()
    {
        $productos = Producto::all(['id', 'nombre', 'stock']);
        return response()->json($productos);
    }

    private function registrarAuditoria($accion, $modelo, $old, $new, $descripcion, $observacion = null)
    {
        try {
            Auditoria::create([
                'user_id' => Auth::id(),
                'action' => $accion,
                'model_type' => get_class($modelo),
                'model_id' => $modelo->id,
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
                'description' => $descripcion,
                'observacion' => $observacion,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}
