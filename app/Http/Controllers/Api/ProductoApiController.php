<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @property \App\Models\Categoria|null $categoria
 * @property \App\Models\User|null $creador
 * @property \App\Models\User|null $modificador
 * @property int|null $productos_count
 */
class ProductoApiController extends Controller
{
    /**
     * Obtener todos los productos (con filtros opcionales)
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

            // Verificar que el usuario tenga un rol válido para ver productos
            $rolesPermitidos = ['Administrador', 'Bodega', 'Ventas', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver productos.'
                ], 403);
            }

            // Construir la consulta base
            $query = Producto::with(['categoria', 'creador']);

            // Si piden eliminados, mostrar solo eliminados (soft deleted)
            if ($request->has('eliminados') && $request->input('eliminados') == 1) {
                $query = $query->onlyTrashed();
            } else {
                $query = $query->where('estado', 'activo')->whereNull('deleted_at');
            }

            // Filtros opcionales
            if ($request->filled('buscar')) {
                $buscar = $request->input('buscar');
                $query->where(function($q) use ($buscar) {
                    $q->where('nombre', 'like', "%$buscar%")
                      ->orWhere('descripcion', 'like', "%$buscar%");
                });
            }

            if ($request->filled('categoria_id')) {
                $query->where('categoria_id', $request->input('categoria_id'));
            }

            if ($request->filled('stock_min')) {
                $query->where('stock', '>=', $request->input('stock_min'));
            }

            if ($request->filled('precio_min')) {
                $query->where('precio', '>=', $request->input('precio_min'));
            }

            if ($request->filled('precio_max')) {
                $query->where('precio', '<=', $request->input('precio_max'));
            }

            // Ordenamiento
            $orden = $request->input('orden', 'nombre');
            $direccion = $request->input('direccion', 'asc');
            $query->orderBy($orden, $direccion);

            // Paginación
            $perPage = $request->input('per_page', 15);
            $productos = $query->paginate($perPage);

            // Transformar los datos para la API
            $productos->getCollection()->transform(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'stock' => $producto->stock,
                    'estado' => $producto->estado,
                    'imagen_url' => $producto->imagen ? asset('storage/productos/' . $producto->imagen) : null,
                    'categoria' => $producto->categoria ? [
                        'id' => $producto->categoria->id,
                        'nombre' => $producto->categoria->nombre,
                        'color' => $producto->categoria->color
                    ] : null,
                    'creador' => $producto->creador ? [
                        'id' => $producto->creador->id,
                        'nombre' => $producto->creador->name
                    ] : null,
                    'created_at' => $producto->created_at ? $producto->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $producto->updated_at ? $producto->updated_at->format('Y-m-d H:i:s') : null,
                    // ¡IMPORTANTE! Incluye el campo deleted_at
                    'deleted_at' => $producto->deleted_at ? $producto->deleted_at->format('Y-m-d H:i:s') : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Productos obtenidos exitosamente',
                'data' => $productos->items(),
                'pagination' => [
                    'current_page' => $productos->currentPage(),
                    'last_page' => $productos->lastPage(),
                    'per_page' => $productos->perPage(),
                    'total' => $productos->total(),
                    'from' => $productos->firstItem(),
                    'to' => $productos->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API productos index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener un producto específico
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

            // Verificar permisos
            $rolesPermitidos = ['Administrador', 'Bodega', 'Ventas', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver productos.'
                ], 403);
            }

            $producto = Producto::with(['categoria', 'creador', 'modificador'])
                ->where('estado', 'activo')
                ->whereNull('deleted_at')
                ->find($id);

            if (!$producto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            $data = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio' => $producto->precio,
                'stock' => $producto->stock,
                'estado' => $producto->estado,
                'imagen_url' => $producto->imagen ? asset('storage/productos/' . $producto->imagen) : null,
                'categoria' => $producto->categoria ? [
                    'id' => $producto->categoria->id,
                    'nombre' => $producto->categoria->nombre,
                    'descripcion' => $producto->categoria->descripcion,
                    'color' => $producto->categoria->color
                ] : null,
                'creador' => $producto->creador ? [
                    'id' => $producto->creador->id,
                    'nombre' => $producto->creador->name,
                    'email' => $producto->creador->email
                ] : null,
                'modificador' => $producto->modificador ? [
                    'id' => $producto->modificador->id,
                    'nombre' => $producto->modificador->name,
                    'email' => $producto->modificador->email
                ] : null,
                'created_at' => $producto->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $producto->updated_at->format('Y-m-d H:i:s')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Producto obtenido exitosamente',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API productos show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener categorías disponibles
     */
    public function categorias(): JsonResponse
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
            $rolesPermitidos = ['Administrador', 'Bodega', 'Ventas', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver categorías.'
                ], 403);
            }

            $categorias = Categoria::where('activo', true)
                ->whereNull('deleted_at')
                ->orderBy('nombre')
                ->get()
                ->map(function ($categoria) {
                    return [
                        'id' => $categoria->id,
                        'nombre' => $categoria->nombre,
                        'descripcion' => $categoria->descripcion,
                        'color' => $categoria->color,
                        'productos_count' => $categoria->productos()->where('estado', 'activo')->count()
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Categorías obtenidas exitosamente',
                'data' => $categorias
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API categorías: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de productos
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
            $rolesPermitidos = ['Administrador', 'Bodega', 'Ventas', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver estadísticas.'
                ], 403);
            }

            $stats = [
                'total_productos' => Producto::where('estado', 'activo')->whereNull('deleted_at')->count(),
                'productos_sin_stock' => Producto::where('estado', 'activo')->whereNull('deleted_at')->where('stock', 0)->count(),
                'productos_bajo_stock' => Producto::where('estado', 'activo')->whereNull('deleted_at')->where('stock', '<=', 5)->where('stock', '>', 0)->count(),
                'valor_total_inventario' => Producto::where('estado', 'activo')->whereNull('deleted_at')->sum(\DB::raw('precio * stock')),
                'categorias_activas' => Categoria::where('activo', true)->whereNull('deleted_at')->count(),
                'productos_por_categoria' => Categoria::where('activo', true)
                    ->whereNull('deleted_at')
                    ->withCount(['productos' => function($query) {
                        $query->where('estado', 'activo')->whereNull('deleted_at');
                    }])
                    ->get()
                    ->map(function($cat) {
                        return [
                            'categoria' => $cat->nombre,
                            'cantidad' => $cat->productos_count
                        ];
                    })
            ];

            return response()->json([
                'success' => true,
                'message' => 'Estadísticas obtenidas exitosamente',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API estadísticas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Crear producto
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric',
                'stock' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id',
                'imagen' => 'nullable|image|max:2048', // imagen opcional
            ]);

            // Manejar imagen si viene
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $filename = uniqid('prod_') . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/productos', $filename);
                $data['imagen'] = $filename;
            }

            $producto = \App\Models\Producto::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado correctamente',
                'data' => $producto
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear producto',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Editar producto
    public function update(Request $request, $id)
    {
        try {
            $producto = \App\Models\Producto::findOrFail($id);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric',
                'stock' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id',
                'imagen' => 'nullable|image|max:2048', // imagen opcional
            ]);

            // Manejar imagen si viene
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $filename = uniqid('prod_') . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/productos', $filename);
                $data['imagen'] = $filename;
            }

            $producto->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente',
                'data' => $producto
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar producto',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Eliminar producto (soft delete)
    public function destroy($id)
    {
        try {
            $producto = \App\Models\Producto::findOrFail($id);
    
            if (!\Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
    
            $user = \Auth::user();
    
            $password = request('password');
            if (!$password || !\Hash::check($password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
    
            $observacion = request('observacion');
            if (!$observacion) {
                return response()->json(['success' => false, 'message' => 'El motivo es obligatorio.'], 422);
            }
    
            $old = $producto->toArray();
            $producto->delete();
    
            // Registrar auditoría
            \App\Models\Auditoria::create([
                'user_id' => $user->id,
                'action' => 'delete',
                'model_type' => get_class($producto),
                'model_id' => $producto->id,
                'old_values' => json_encode($old),
                'new_values' => null,
                'description' => 'Producto eliminado (soft)',
                'observacion' => $observacion,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Restaurar producto eliminado (soft delete)
    public function restore(Request $request, $id)
    {
        try {
            if (!\Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
            $user = \Auth::user();

            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);

            if (!\Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }

            $producto = \App\Models\Producto::onlyTrashed()->findOrFail($id);
            $producto->restore();

            // Auditoría
            \App\Models\Auditoria::create([
                'user_id' => $user->id,
                'action' => 'restore',
                'model_type' => get_class($producto),
                'model_id' => $producto->id,
                'old_values' => null,
                'new_values' => json_encode($producto->toArray()),
                'description' => 'Producto restaurado',
                'observacion' => $request->observacion,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto restaurado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar producto',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // Eliminar producto definitivamente (force delete)
    public function forceDelete(Request $request, $id)
    {
        try {
            if (!\Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
            $user = \Auth::user();

            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);

            if (!\Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }

            $producto = \App\Models\Producto::onlyTrashed()->findOrFail($id);

            // (Opcional) Validar que no tenga facturas asociadas, como en la web
            if ($producto->facturaDetalles()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el producto porque está asociado a facturas. Elimine primero las facturas relacionadas.'
                ], 422);
            }

            $old = $producto->toArray();

            // Borrar imagen física si existe
            if ($producto->imagen) {
                $publicPath = public_path('storage/productos/' . $producto->imagen);
                $backupPath = storage_path('app/public/productos/' . $producto->imagen);
                if (file_exists($publicPath)) {
                    @unlink($publicPath);
                }
                if (file_exists($backupPath)) {
                    @unlink($backupPath);
                }
            }

            $producto->forceDelete();

            // Auditoría
            \App\Models\Auditoria::create([
                'user_id' => $user->id,
                'action' => 'forceDelete',
                'model_type' => get_class($producto),
                'model_id' => $producto->id,
                'old_values' => json_encode($old),
                'new_values' => null,
                'description' => 'Producto eliminado permanentemente',
                'observacion' => $request->observacion,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado permanentemente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar permanentemente el producto',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 