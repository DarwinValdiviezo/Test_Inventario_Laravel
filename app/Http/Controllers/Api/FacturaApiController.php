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

            // Verificar que el usuario tenga un rol válido para ver facturas
            $rolesPermitidos = ['Administrador', 'Ventas'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver facturas.'
                ], 403);
            }

            // Construir la consulta
            $query = Factura::with(['cliente', 'usuario', 'detalles.producto'])
                ->whereNull('deleted_at');

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

            // Verificar permisos
            $rolesPermitidos = ['Administrador', 'Ventas'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver facturas.'
                ], 403);
            }

            $factura = Factura::with(['cliente', 'usuario', 'detalles.producto'])
                ->whereNull('deleted_at')
                ->find($id);

            if (!$factura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada'
                ], 404);
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
} 