<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreProductoRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductosExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductosController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::with('categoria');
        $logs = collect();
        $usuarios = collect();
        if ($request->has('eliminados')) {
            $query = $query->onlyTrashed();
            // Obtener logs de auditoría para productos eliminados con filtros
            $logsQuery = \App\Models\Auditoria::where('model_type', Producto::class)
                ->whereIn('action', ['delete', 'restore', 'forceDelete'])
                ->with('user');
            if ($request->filled('log_buscar')) {
                $logBuscar = $request->input('log_buscar');
                $logsQuery = $logsQuery->where(function($q) use ($logBuscar) {
                    $q->where('description', 'like', "%$logBuscar%")
                      ->orWhere('observacion', 'like', "%$logBuscar%")
                      ->orWhereHas('user', function($userQuery) use ($logBuscar) {
                          $userQuery->where('name', 'like', "%$logBuscar%") ;
                      });
                });
            }
            if ($request->filled('log_accion')) {
                $logsQuery = $logsQuery->where('action', $request->input('log_accion'));
            }
            if ($request->filled('log_usuario')) {
                $logsQuery = $logsQuery->whereHas('user', function($q) use ($request) {
                    $q->where('id', $request->input('log_usuario'));
                });
            }
            if ($request->filled('log_fecha_desde')) {
                $logsQuery = $logsQuery->whereDate('created_at', '>=', $request->input('log_fecha_desde'));
            }
            if ($request->filled('log_fecha_hasta')) {
                $logsQuery = $logsQuery->whereDate('created_at', '<=', $request->input('log_fecha_hasta'));
            }
            $logs = $logsQuery->orderBy('created_at', 'desc')
                ->paginate($request->input('log_per_page', 10))
                ->withQueryString();
            $usuarios = \App\Models\User::orderBy('name')->get();
        }
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                  ->orWhere('descripcion', 'like', "%$buscar%") ;
            });
        }
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->input('categoria_id'));
        }
        $perPage = $request->input('per_page', 10);
        $productos = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $categorias = \App\Models\Categoria::where('activo', true)->orderBy('nombre')->get();
        return view('productos.index', compact('productos', 'categorias', 'logs', 'usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = \App\Models\Categoria::where('activo', true)->orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request)
    {
        \DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            // Log para depuración
            \Log::info('¿Archivo recibido en store?', ['hasFile' => $request->hasFile('imagen')]);
            if ($request->hasFile('imagen')) {
                \Log::info('Info del archivo en store', [
                    'originalName' => $request->file('imagen')->getClientOriginalName(),
                    'mime' => $request->file('imagen')->getMimeType(),
                    'size' => $request->file('imagen')->getSize(),
                ]);
                $file = $request->file('imagen');
                $filename = uniqid('prod_') . '.' . $file->getClientOriginalExtension();
                \Log::info('Ruta absoluta donde se guardará la imagen (store)', [
                    'path' => storage_path('app/public/productos/' . $filename)
                ]);
                $file->move(storage_path('app/public/productos'), $filename);
                \Log::info('Imagen guardada en store', ['filename' => $filename]);
                $data['imagen'] = $filename;
            }
            $producto = Producto::create($data);
            $this->registrarAuditoria('create', $producto, null, $data, 'Producto creado');
            \DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto creado exitosamente.',
                    'redirect' => route('productos.show', $producto)
                ]);
            }
            return redirect()->route('productos.show', $producto)->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al crear producto: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el producto. Intenta de nuevo o contacta al administrador.'
                ], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Error al crear el producto.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $producto = Producto::withTrashed()->with(['categoria', 'creador', 'modificador'])->findOrFail($id);
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $categorias = \App\Models\Categoria::where('activo', true)->orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductoRequest $request, $id)
    {
        \DB::beginTransaction();
        try {
            $producto = Producto::withTrashed()->findOrFail($id);
            $this->authorize('update', $producto);
            $old = $producto->toArray();
            $data = $request->validated();
            $hasChanges = false;
            $fieldsToCheck = ['nombre', 'descripcion', 'categoria_id', 'stock', 'precio'];
            foreach ($fieldsToCheck as $field) {
                if (array_key_exists($field, $data) && $data[$field] != $producto->$field) {
                    $hasChanges = true;
                    break;
                }
            }
            // Imagen
            if ($request->hasFile('imagen')) {
                $hasChanges = true;
                // Borrar imagen anterior si existe
                if ($producto->imagen && \Storage::exists('public/productos/' . $producto->imagen)) {
                    \Storage::delete('public/productos/' . $producto->imagen);
                }
                $file = $request->file('imagen');
                $filename = uniqid('prod_') . '.' . $file->getClientOriginalExtension();
                $file->move(storage_path('app/public/productos'), $filename);
                $data['imagen'] = $filename;
            } else {
                unset($data['imagen']);
            }
            if (!$hasChanges) {
                \DB::rollBack();
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'No se realizaron cambios en el producto "' . $producto->nombre . '".',
                        'redirect' => route('productos.index')
                    ]);
                }
                return redirect()->route('productos.index')
                    ->with('info', 'No se realizaron cambios en el producto "' . $producto->nombre . '".');
            }
            $data['updated_by'] = auth()->id();
            $producto->update($data);
            $this->registrarAuditoria('update', $producto, $old, $data, 'Producto actualizado');
            \DB::commit();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto "' . $producto->nombre . '" actualizado exitosamente.',
                    'redirect' => route('productos.index')
                ]);
            }
            return redirect()->route('productos.index')
                ->with('success', 'Producto "' . $producto->nombre . '" actualizado exitosamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al actualizar producto: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el producto. Intenta de nuevo o contacta al administrador.'
                ], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el producto. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            $producto = Producto::findOrFail($id);
            $this->authorize('delete', $producto);
            // Validación manual
            if (empty($request->password)) {
                \DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'errors' => ['password' => ['La contraseña es requerida.']]
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['password' => 'La contraseña es requerida.'])
                    ->withInput()
                    ->with('modal', 'modalEliminarProducto'.$id);
            }
            if (empty($request->observacion)) {
                \DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'errors' => ['observacion' => ['La observación es requerida.']]
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['observacion' => 'La observación es requerida.'])
                    ->withInput()
                    ->with('modal', 'modalEliminarProducto'.$id);
            }
            if (!\Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'errors' => ['password' => ['Contraseña incorrecta.']]
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['password' => 'Contraseña incorrecta.'])
                    ->withInput()
                    ->with('modal', 'modalEliminarProducto'.$id);
            }
            $old = $producto->toArray();
            $producto->delete();
            $this->registrarAuditoria('delete', $producto, $old, null, 'Producto eliminado (soft)', $request->observacion);
            \DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado temporalmente.'
                ]);
            }
            return redirect()->route('productos.index')->with('success', 'Producto eliminado temporalmente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al eliminar producto: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'errors' => ['general' => ['Error al eliminar el producto.']]
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al eliminar el producto.');
        }
    }

    public function restore(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ], [
                'password.required' => 'La contraseña es requerida.',
                'observacion.required' => 'La observación es requerida.',
            ]);
            if (!\Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'errors' => ['password' => ['Contraseña incorrecta.']]
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['password' => 'Contraseña incorrecta.'])
                    ->withInput()
                    ->with('modal', 'modalRestaurarProducto'.$id);
            }
            $producto = Producto::onlyTrashed()->findOrFail($id);
            $this->authorize('restore', $producto);
            $producto->restore();
            $this->registrarAuditoria('restore', $producto, null, $producto->toArray(), 'Producto restaurado', $request->observacion);
            \DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto restaurado.'
                ]);
            }
            return redirect()->route('productos.index', ['eliminados' => 1])->with('success', 'Producto restaurado.');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            \DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $ve->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($ve->errors())
                ->withInput()
                ->with('modal', 'modalRestaurarProducto'.$id);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al restaurar producto: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'errors' => ['general' => ['Error al restaurar el producto.']]
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al restaurar el producto.');
        }
    }

    public function forceDelete(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ], [
                'password.required' => 'La contraseña es requerida.',
                'observacion.required' => 'La observación es requerida.',
            ]);
            if (!\Hash::check($request->password, auth()->user()->password)) {
                \DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'errors' => ['password' => ['Contraseña incorrecta.']]
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['password' => 'Contraseña incorrecta.'])
                    ->withInput()
                    ->with('modal', 'modalBorrarDefinitivoProducto'.$id);
            }
            $producto = Producto::onlyTrashed()->findOrFail($id);
            $this->authorize('forceDelete', $producto);
            // VALIDACIÓN: No permitir si tiene detalles de factura
            if ($producto->facturaDetalles()->count() > 0) {
                \DB::rollBack();
                $msg = 'No se puede eliminar el producto porque está asociado a facturas. Elimine primero las facturas relacionadas.';
                if ($request->ajax()) {
                    return response()->json([
                        'errors' => ['general' => [$msg]]
                    ], 422);
                }
                return redirect()->back()->with('error', $msg);
            }
            $old = $producto->toArray();
            if ($producto->imagen && \Storage::exists('public/productos/' . $producto->imagen)) {
                \Storage::delete('public/productos/' . $producto->imagen);
            }
            $producto->forceDelete();
            $this->registrarAuditoria('forceDelete', $producto, $old, null, 'Producto eliminado permanentemente', $request->observacion);
            \DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Producto eliminado permanentemente.'
                ]);
            }
            return redirect()->route('productos.index', ['eliminados' => 1])->with('success', 'Producto eliminado permanentemente.');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            \DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $ve->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($ve->errors())
                ->withInput()
                ->with('modal', 'modalBorrarDefinitivoProducto'.$id);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al eliminar permanentemente producto: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'errors' => ['general' => ['Error al eliminar permanentemente el producto.']]
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al eliminar permanentemente el producto.');
        }
    }

    /**
     * Exportar productos (CSV, Excel, PDF)
     */
    public function export($type, Request $request)
    {
        if ($type === 'csv') {
            $productos = \App\Models\Producto::with('categoria')->get();
            $filename = 'productos_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            $callback = function() use ($productos) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['ID', 'Nombre', 'Categoría', 'Stock', 'Precio', 'Estado']);
                foreach ($productos as $p) {
                    fputcsv($handle, [
                        $p->id,
                        $p->nombre,
                        $p->categoria->nombre ?? 'Sin categoría',
                        $p->stock,
                        $p->precio,
                        $p->deleted_at ? 'Eliminado' : 'Activo',
                    ]);
                }
                fclose($handle);
            };
            return response()->stream($callback, 200, $headers);
        }
        if ($type === 'excel') {
            return Excel::download(new ProductosExport, 'productos_' . now()->format('Ymd_His') . '.xlsx');
        }
        if ($type === 'pdf') {
            $productos = \App\Models\Producto::with('categoria')->get();
            $pdf = Pdf::loadView('productos.pdf', compact('productos'));
            return $pdf->download('productos_' . now()->format('Ymd_His') . '.pdf');
        }
        return back()->with('error', 'Tipo de exportación no soportado.');
    }

    /**
     * Reporte gráfico de productos
     */
    public function reporte(Request $request)
    {
        $productos = \App\Models\Producto::select('nombre', 'stock')->orderBy('nombre')->get();
        $labels = $productos->pluck('nombre');
        $data = $productos->pluck('stock');
        return view('productos.reporte', compact('labels', 'data'));
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
