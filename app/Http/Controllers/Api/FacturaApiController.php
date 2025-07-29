<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @property string|null $mes
 * @property int|null $cantidad
 * @property float|null $total_ventas
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Factura[] $facturas
 */
class FacturaApiController extends Controller
{
    /**
     * Obtener todas las facturas (con filtros opcionales)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado. Token requerido.'
                ], 401);
            }

            $user = Auth::user();

            // Permitir a clientes ver solo sus facturas
            if ($user->hasRole('cliente')) {
                $cliente = $user->cliente;
                if (!$cliente) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró el cliente asociado a este usuario.'
                    ], 404);
                }
                $query = Factura::with(['cliente', 'usuario', 'detalles.producto'])
                    ->where('cliente_id', $cliente->id)
                    ->whereNull('deleted_at');
            } else {
                $rolesPermitidos = ['Administrador', 'Ventas'];
                if (!$user->hasAnyRole($rolesPermitidos)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para ver facturas.'
                    ], 403);
                }
                $query = Factura::with(['cliente', 'usuario', 'detalles.producto'])
                    ->whereNull('deleted_at');
            }

            // Filtros opcionales
            if ($request->filled('estado')) {
                $query->where('estado', $request->input('estado'));
            }

            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->input('cliente_id'));
            }

            if ($request->filled('usuario_id')) {
                $query->where('usuario_id', $request->input('usuario_id'));
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
            }

            if ($request->filled('total_min')) {
                $query->where('total', '>=', $request->input('total_min'));
            }

            if ($request->filled('total_max')) {
                $query->where('total', '<=', $request->input('total_max'));
            }

            // Ordenamiento
            $orden = $request->input('orden', 'created_at');
            $direccion = $request->input('direccion', 'desc');
            $query->orderBy($orden, $direccion);

            // Paginación
            $perPage = $request->input('per_page', 15);
            $facturas = $query->paginate($perPage);

            // Transformar los datos para la API
            $facturas->getCollection()->transform(function ($factura) {
                return [
                    'id' => $factura->id,
                    'numero_secuencial' => $factura->getNumeroFormateado(),
                    'cua' => $factura->getCUAFormateado(),
                    'subtotal' => $factura->subtotal,
                    'iva' => $factura->iva,
                    'total' => $factura->total,
                    'estado' => $factura->estado,
                    'estado_firma' => $factura->estado_firma,
                    'estado_emision' => $factura->estado_emision,
                    'forma_pago' => $factura->forma_pago,
                    'fecha_emision' => $factura->fecha_emision ? $factura->fecha_emision->format('Y-m-d') : null,
                    'fecha_firma' => $factura->fecha_firma ? $factura->fecha_firma->format('Y-m-d H:i:s') : null,
                    'fecha_emision_email' => $factura->fecha_emision_email ? $factura->fecha_emision_email->format('Y-m-d H:i:s') : null,
                    'cliente' => $factura->cliente ? [
                        'id' => $factura->cliente->id,
                        'nombre' => $factura->cliente->nombre,
                        'email' => $factura->cliente->email,
                        'telefono' => $factura->cliente->telefono
                    ] : null,
                    'usuario' => $factura->usuario ? [
                        'id' => $factura->usuario->id,
                        'name' => $factura->usuario->name,
                        'email' => $factura->usuario->email
                    ] : null,
                    'detalles_count' => $factura->detalles->count(),
                    'created_at' => $factura->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $factura->updated_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Facturas obtenidas exitosamente',
                'data' => $facturas->items(),
                'pagination' => [
                    'current_page' => $facturas->currentPage(),
                    'last_page' => $facturas->lastPage(),
                    'per_page' => $facturas->perPage(),
                    'total' => $facturas->total(),
                    'from' => $facturas->firstItem(),
                    'to' => $facturas->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API facturas index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener una factura específica
     */
    public function show($id): JsonResponse
    {
        try {
            // Verificar autenticación
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado. Token requerido.'
                ], 401);
            }

            $user = Auth::user();

            // Permitir a clientes ver solo sus facturas
            $factura = Factura::with(['cliente', 'usuario', 'detalles.producto'])
                ->whereNull('deleted_at')
                ->find($id);

            if ($user->hasRole('cliente')) {
                $cliente = $user->cliente;
                if (!$cliente) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró el cliente asociado a este usuario.'
                    ], 404);
                }
                if (!$factura || $factura->cliente_id !== $cliente->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para ver esta factura.'
                    ], 403);
                }
            } else {
                $rolesPermitidos = ['Administrador', 'Ventas'];
                if (!$user->hasAnyRole($rolesPermitidos)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permisos para ver facturas.'
                    ], 403);
                }
                if (!$factura) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Factura no encontrada'
                    ], 404);
                }
            }

            $data = [
                'id' => $factura->id,
                'numero_secuencial' => $factura->getNumeroFormateado(),
                'cua' => $factura->getCUAFormateado(),
                'subtotal' => $factura->subtotal,
                'iva' => $factura->iva,
                'total' => $factura->total,
                'estado' => $factura->estado,
                'estado_firma' => $factura->estado_firma,
                'estado_emision' => $factura->estado_emision,
                'forma_pago' => $factura->forma_pago,
                'fecha_emision' => $factura->fecha_emision ? $factura->fecha_emision->format('Y-m-d') : null,
                'fecha_firma' => $factura->fecha_firma ? $factura->fecha_firma->format('Y-m-d H:i:s') : null,
                'fecha_emision_email' => $factura->fecha_emision_email ? $factura->fecha_emision_email->format('Y-m-d H:i:s') : null,
                'cliente' => $factura->cliente ? [
                    'id' => $factura->cliente->id,
                    'nombre' => $factura->cliente->nombre,
                    'email' => $factura->cliente->email,
                    'telefono' => $factura->cliente->telefono,
                    'direccion' => $factura->cliente->direccion
                ] : null,
                'usuario' => $factura->usuario ? [
                    'id' => $factura->usuario->id,
                    'name' => $factura->usuario->name,
                    'email' => $factura->usuario->email
                ] : null,
                'detalles' => $factura->detalles->map(function ($detalle) {
                    return [
                        'id' => $detalle->id,
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal' => $detalle->subtotal,
                        'producto' => $detalle->producto ? [
                            'id' => $detalle->producto->id,
                            'nombre' => $detalle->producto->nombre,
                            'descripcion' => $detalle->producto->descripcion,
                            'imagen_url' => $detalle->producto->imagen ? asset('storage/productos/' . $detalle->producto->imagen) : null
                        ] : null
                    ];
                }),
                'created_at' => $factura->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $factura->updated_at->format('Y-m-d H:i:s')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Factura obtenida exitosamente',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API facturas show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener clientes disponibles
     */
    public function clientes(): JsonResponse
    {
        try {
            // Verificar autenticación
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado. Token requerido.'
                ], 401);
            }

            $user = Auth::user();

            // Verificar permisos
            $rolesPermitidos = ['Administrador', 'Ventas'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver clientes.'
                ], 403);
            }

            $clientes = Cliente::where('estado', 'activo')
                ->whereNull('deleted_at')
                ->orderBy('nombre')
                ->get()
                ->map(function ($cliente) {
                    return [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre,
                        'email' => $cliente->email,
                        'telefono' => $cliente->telefono,
                        'direccion' => $cliente->direccion,
                        'facturas_count' => $cliente->facturas()->whereNull('deleted_at')->count()
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Clientes obtenidos exitosamente',
                'data' => $clientes
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API clientes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de facturas
     */
    public function estadisticas(): JsonResponse
    {
        try {
            // Verificar autenticación
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado. Token requerido.'
                ], 401);
            }

            $user = Auth::user();

            // Verificar permisos
            $rolesPermitidos = ['Administrador', 'Ventas'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver estadísticas.'
                ], 403);
            }

            $stats = [
                'total_facturas' => Factura::whereNull('deleted_at')->count(),
                'facturas_activas' => Factura::whereNull('deleted_at')->where('estado', 'activa')->count(),
                'facturas_anuladas' => Factura::whereNull('deleted_at')->where('estado', 'anulada')->count(),
                'total_ventas' => Factura::whereNull('deleted_at')->where('estado', 'activa')->sum('total'),
                'total_iva' => Factura::whereNull('deleted_at')->where('estado', 'activa')->sum('iva'),
                'facturas_firmadas' => Factura::whereNull('deleted_at')->where('estado_firma', 'FIRMADA')->count(),
                'facturas_emitidas' => Factura::whereNull('deleted_at')->where('estado_emision', 'EMITIDA')->count(),
                'clientes_activos' => Cliente::where('estado', 'activo')->whereNull('deleted_at')->count(),
                'facturas_por_mes' => Factura::whereNull('deleted_at')
                    ->whereYear('created_at', now()->year)
                    ->selectRaw('MONTH(created_at) as mes, COUNT(*) as cantidad, SUM(total) as total_ventas')
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get()
                    ->map(function($item) {
                        return [
                            'mes' => $item->mes,
                            'cantidad' => $item->cantidad,
                            'total_ventas' => $item->total_ventas
                        ];
                    })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API estadísticas facturas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Descargar PDF de la factura
     */
    public function downloadPDF($id)
    {
        try {
            $factura = Factura::withTrashed()->with(['cliente', 'usuario', 'detalles.producto'])->findOrFail($id);
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
            // Usar DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('facturas.pdf', compact('factura'));
            return $pdf->download('factura-' . $factura->id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error al descargar PDF: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al generar PDF', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Enviar factura por email
     */
    public function sendEmail(Request $request, $id)
    {
        try {
            $factura = Factura::withTrashed()->with(['cliente', 'usuario', 'detalles.producto'])->findOrFail($id);
            $request->validate([
                'email' => 'required|email',
                'asunto' => 'required|string|max:255',
                'mensaje' => 'nullable|string|max:500',
            ]);
            $emailService = new \App\Services\EmailService();
            $resultado = $emailService->enviarFactura(
                $factura,
                $request->email,
                $request->asunto,
                $request->mensaje ?? ''
            );
            if ($resultado) {
                return response()->json(['success' => true, 'message' => 'Factura enviada exitosamente a ' . $request->email]);
            } else {
                return response()->json(['success' => false, 'message' => 'Error al enviar la factura por email.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar factura por email: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al enviar factura por email', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Firmar digitalmente una factura
     */
    public function firmar(Request $request, $id)
    {
        try {
            $factura = Factura::findOrFail($id);
            if (!$factura->isPendienteFirma()) {
                return response()->json(['success' => false, 'message' => 'La factura ya está firmada o no puede ser firmada.'], 400);
            }
            $factura->firmarDigitalmente();
            return response()->json(['success' => true, 'message' => 'Factura firmada digitalmente correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error al firmar factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al firmar factura', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Emitir una factura (enviar por email)
     */
    public function emitir(Request $request, $id)
    {
        try {
            $factura = Factura::findOrFail($id);
            if (!$factura->isFirmada()) {
                return response()->json(['success' => false, 'message' => 'La factura debe estar firmada antes de emitirla.'], 400);
            }
            if ($factura->isEmitida()) {
                return response()->json(['success' => false, 'message' => 'La factura ya ha sido emitida.'], 400);
            }
            $factura->emitir();
            return response()->json(['success' => true, 'message' => 'Factura emitida correctamente. Se enviará por email al cliente.']);
        } catch (\Exception $e) {
            Log::error('Error al emitir factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al emitir factura', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Restaurar factura eliminada (soft delete)
     */
    public function restore(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);
            if (!\Hash::check($request->password, Auth::user()->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $factura = Factura::onlyTrashed()->findOrFail($id);
            // Actualizar stock al restaurar
            foreach ($factura->detalles as $detalle) {
                $producto = \App\Models\Producto::find($detalle->producto_id);
                if ($producto) {
                    if ($producto->stock < $detalle->cantidad) {
                        return response()->json(['success' => false, 'message' => 'Stock insuficiente para restaurar la factura. Producto: ' . $producto->nombre], 422);
                    }
                    $producto->stock -= $detalle->cantidad;
                    $producto->save();
                }
            }
            $factura->restore();
            // Auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'restore',
                    'model_type' => Factura::class,
                    'model_id' => $factura->id,
                    'old_values' => null,
                    'new_values' => json_encode($factura->toArray()),
                    'description' => 'Factura restaurada y stock actualizado',
                    'observacion' => $request->observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Factura restaurada correctamente y stock actualizado.']);
        } catch (\Exception $e) {
            Log::error('Error al restaurar factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al restaurar factura', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Eliminar factura definitivamente (force delete)
     */
    public function forceDelete(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);
            if (!\Hash::check($request->password, Auth::user()->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $factura = Factura::onlyTrashed()->findOrFail($id);
            $old = $factura->toArray();
            \App\Models\FacturaDetalle::where('factura_id', $factura->id)->forceDelete();
            $factura->forceDelete();
            // Auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'forceDelete',
                    'model_type' => Factura::class,
                    'model_id' => $factura->id,
                    'old_values' => json_encode($old),
                    'new_values' => null,
                    'description' => 'Factura eliminada permanentemente',
                    'observacion' => $request->observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Factura eliminada permanentemente.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar permanentemente la factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar permanentemente la factura', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Eliminar (anular) factura (soft delete)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string|min:10',
            ]);
            if (!\Hash::check($request->password, Auth::user()->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $factura = Factura::findOrFail($id);
            // Revertir stock al anular
            foreach ($factura->detalles as $detalle) {
                $producto = \App\Models\Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->stock += $detalle->cantidad;
                    $producto->save();
                }
            }
            $old = $factura->toArray();
            $factura->delete();
            // Auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'delete',
                    'model_type' => Factura::class,
                    'model_id' => $factura->id,
                    'old_values' => json_encode($old),
                    'new_values' => null,
                    'description' => 'Factura anulada (soft) y stock revertido',
                    'observacion' => $request->observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Factura anulada correctamente y stock revertido.']);
        } catch (\Exception $e) {
            Log::error('Error al anular factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al anular factura', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Listar facturas del usuario autenticado (cliente)
     */
    public function misFacturas(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
            // Solo clientes pueden usar este endpoint
            if (!$user->hasRole('cliente')) {
                return response()->json(['success' => false, 'message' => 'Solo los clientes pueden ver sus facturas.'], 403);
            }
            $cliente = $user->cliente;
            if (!$cliente) {
                return response()->json(['success' => false, 'message' => 'No se encontró el cliente asociado a este usuario.'], 404);
            }
            $facturas = Factura::with(['detalles.producto'])
                ->where('cliente_id', $cliente->id)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->get();
            $data = $facturas->map(function ($factura) {
                return [
                    'id' => $factura->id,
                    'numero_secuencial' => $factura->getNumeroFormateado(),
                    'subtotal' => $factura->subtotal,
                    'iva' => $factura->iva,
                    'total' => $factura->total,
                    'estado' => $factura->estado,
                    'created_at' => $factura->created_at->format('Y-m-d H:i:s'),
                    'detalles' => $factura->detalles->map(function ($detalle) {
                        return [
                            'producto' => $detalle->producto ? $detalle->producto->nombre : null,
                            'cantidad' => (int) $detalle->cantidad,
                            'precio_unitario' => (float) $detalle->precio_unitario,
                            'subtotal' => (float) $detalle->subtotal,
                        ];
                    })->toArray(),
                ];
            });
            return response()->json(['success' => true, 'message' => 'Facturas del cliente obtenidas exitosamente', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error al obtener facturas del cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al obtener facturas del cliente', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Ver estado de una factura
     */
    public function estado($id)
    {
        try {
            $factura = Factura::findOrFail($id);
            $estadoVisual = method_exists($factura, 'getEstadoVisual') ? $factura->getEstadoVisual() : $factura->estado;
            return response()->json([
                'success' => true,
                'estado' => $estadoVisual,
                'isFirmada' => method_exists($factura, 'isFirmada') ? $factura->isFirmada() : null,
                'isEmitida' => method_exists($factura, 'isEmitida') ? $factura->isEmitida() : null,
                'isPendienteFirma' => method_exists($factura, 'isPendienteFirma') ? $factura->isPendienteFirma() : null,
                'isPendienteEmision' => method_exists($factura, 'isPendienteEmision') ? $factura->isPendienteEmision() : null,
                'fechaFirma' => $factura->fecha_firma,
                'fechaEmision' => $factura->fecha_emision_email
            ]);
        } catch (\Exception $e) {
            Log::error('Error al consultar estado de factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al consultar estado de factura', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }
} 