<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use App\Models\Categoria;
use App\Models\FacturaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ADMINISTRADOR: dashboard original
        if ($user->hasRole('Administrador')) {
            // --- Lógica original ---
            $clientesActivos = \App\Models\Cliente::where('estado', 'activo')->count();
            $totalProductos = \App\Models\Producto::sum('stock');
            $facturasMes = \App\Models\Factura::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();
            $ventasMes = \App\Models\Factura::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->sum('total');
            $productosBajoStock = \App\Models\Producto::where('stock', '<', 10)
                                         ->with('categoria')
                                         ->orderBy('stock', 'asc')
                                         ->limit(5)
                                         ->get();
            $topProductos = \App\Models\FacturaDetalle::select('producto_id', \DB::raw('SUM(cantidad) as total_vendido'))
                                         ->with('producto.categoria')
                                         ->groupBy('producto_id')
                                         ->orderBy('total_vendido', 'desc')
                                         ->limit(5)
                                         ->get();
            $dias = range(1, now()->daysInMonth);
            $ventasEsteMes = [];
            $ventasMesPasado = [];
            $mesActual = now()->month;
            $anioActual = now()->year;
            $mesPasado = now()->subMonth()->month;
            $anioPasado = now()->subMonth()->year;
            foreach ($dias as $dia) {
                $ventasEsteMes[] = \App\Models\Factura::whereDay('created_at', $dia)
                    ->whereMonth('created_at', $mesActual)
                    ->whereYear('created_at', $anioActual)
                    ->sum('total');
                $ventasMesPasado[] = \App\Models\Factura::whereDay('created_at', $dia)
                    ->whereMonth('created_at', $mesPasado)
                    ->whereYear('created_at', $anioPasado)
                    ->sum('total');
            }
            $productosPorCategoria = \App\Models\Categoria::withCount('productos')
                                             ->withSum('productos', 'stock')
                                             ->get();
            $facturasRecientes = \App\Models\Factura::with('cliente')
                                       ->orderBy('created_at', 'desc')
                                       ->limit(5)
                                       ->get();
            $productosSinStock = \App\Models\Producto::where('stock', 0)->count();
            $facturasPendientes = \App\Models\Factura::where('estado', 'activa')->count();
            $tasaConversion = $clientesActivos > 0 ? round(($facturasMes / $clientesActivos) * 100, 1) : 0;
            $top3Productos = $topProductos->take(3);
            $movimientosRecientes = \App\Models\FacturaDetalle::with(['producto', 'factura'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $entradasSalidasDias = [];
            foreach ($dias as $dia) {
                $salidas = \App\Models\FacturaDetalle::whereDay('created_at', $dia)
                    ->whereMonth('created_at', $mesActual)
                    ->whereYear('created_at', $anioActual)
                    ->sum('cantidad');
                $entradas = 0;
                $entradasSalidasDias[] = [
                    'dia' => $dia,
                    'entradas' => $entradas,
                    'salidas' => $salidas,
                ];
            }
            $ultimosClientes = \App\Models\Cliente::orderBy('created_at', 'desc')->limit(5)->get();
            $logsAuditoria = \App\Models\Auditoria::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
            return view('dashboard', compact(
                'clientesActivos',
                'totalProductos',
                'facturasMes',
                'ventasMes',
                'productosBajoStock',
                'topProductos',
                'productosPorCategoria',
                'facturasRecientes',
                'productosSinStock',
                'facturasPendientes',
                'tasaConversion',
                'ventasEsteMes',
                'ventasMesPasado',
                'dias',
                'top3Productos',
                'movimientosRecientes',
                'entradasSalidasDias',
                'ultimosClientes',
                'logsAuditoria'
            ));
        }

        // CLIENTE
        if ($user->hasRole('cliente')) {
            $cliente = $user->cliente;
            $comprasCliente = $cliente ? $cliente->facturas()->count() : 0;
            $facturasCliente = $cliente ? $cliente->facturas()->count() : 0;
            $totalGastado = $cliente ? $cliente->facturas()->sum('total') : 0;
            return view('dashboard_cliente', compact('comprasCliente', 'facturasCliente', 'totalGastado'));
        }

        // SECRETARIO
        if ($user->hasRole('Secretario')) {
            $usuariosActivos = \App\Models\User::where('estado', 'activo')->count();
            $clientesActivos = \App\Models\Cliente::where('estado', 'activo')->count();
            return view('dashboard_secretario', compact('usuariosActivos', 'clientesActivos'));
        }

        // BODEGA
        if ($user->hasRole('Bodega')) {
            $totalProductos = \App\Models\Producto::sum('stock');
            $productosBajoStock = \App\Models\Producto::where('stock', '<', 10)->get();
            return view('dashboard_bodega', compact('totalProductos', 'productosBajoStock'));
        }

        // VENTAS
        if ($user->hasRole('Ventas')) {
            $facturasMes = \App\Models\Factura::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();
            $ventasMes = \App\Models\Factura::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->sum('total');
            $ticketPromedio = $facturasMes > 0 ? $ventasMes / $facturasMes : 0;
            return view('dashboard_ventas', compact('facturasMes', 'ventasMes', 'ticketPromedio'));
        }

        // Si no tiene rol válido
        abort(403, 'Rol no autorizado');
    }
}
