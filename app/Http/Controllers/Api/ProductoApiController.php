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

            // Construir la consulta
            $query = Producto::with(['categoria', 'creador'])
                ->where('estado', 'activo')
                ->whereNull('deleted_at');

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
                    'created_at' => $producto->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $producto->updated_at->format('Y-m-d H:i:s')
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
} 