<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auditoria::query();
        
        // Filtros automáticos
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Filtro por IP si se proporciona
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $logs = $query->with('user')->orderBy('created_at', 'desc')->paginate(25);

        // Estadísticas para el dashboard
        $stats = $this->getAuditStats();
        
        // Datos para filtros automáticos
        $users = User::select('id', 'name', 'email')->get();
        $actions = Auditoria::select('action')->distinct()->pluck('action');
        $modelTypes = Auditoria::select('model_type')->distinct()->pluck('model_type');
        $ipAddresses = Auditoria::select('ip_address')->distinct()->whereNotNull('ip_address')->pluck('ip_address');

        // Datos para reportes
        $recentActivity = Auditoria::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activityByUser = Auditoria::with('user')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $activityByAction = Auditoria::select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->orderBy('total', 'desc')
            ->get();

        $activityByModel = Auditoria::select('model_type', DB::raw('count(*) as total'))
            ->groupBy('model_type')
            ->orderBy('total', 'desc')
            ->get();

        return view('auditorias.index', compact(
            'logs', 
            'stats', 
            'users', 
            'actions', 
            'modelTypes', 
            'ipAddresses',
            'recentActivity',
            'activityByUser',
            'activityByAction',
            'activityByModel'
        ));
    }

    /**
     * Obtener estadísticas de auditoría
     */
    private function getAuditStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_logs' => Auditoria::count(),
            'today_logs' => Auditoria::whereDate('created_at', $today)->count(),
            'this_month_logs' => Auditoria::whereDate('created_at', '>=', $thisMonth)->count(),
            'last_month_logs' => Auditoria::whereDate('created_at', '>=', $lastMonth)
                ->whereDate('created_at', '<', $thisMonth)->count(),
            'unique_users' => Auditoria::distinct('user_id')->count(),
            'actions_count' => [
                'create' => Auditoria::where('action', 'create')->count(),
                'update' => Auditoria::where('action', 'update')->count(),
                'delete' => Auditoria::where('action', 'delete')->count(),
            ],
            'models_count' => Auditoria::select('model_type', DB::raw('count(*) as total'))
                ->groupBy('model_type')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    /**
     * Exportar reporte de auditoría
     */
    public function export(Request $request)
    {
        $query = Auditoria::query();
        
        // Aplicar mismos filtros que en index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $logs = $query->with('user')->orderBy('created_at', 'desc')->get();

        $filename = 'auditoria_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Headers del CSV
            fputcsv($file, [
                'Fecha', 'Usuario', 'Email', 'Acción', 'Modelo', 'ID Modelo', 
                'Afectado', 'Descripción', 'IP Address', 'User Agent'
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->user ? $log->user->name : 'Desconocido',
                    $log->user ? $log->user->email : '-',
                    $log->action,
                    class_basename($log->model_type),
                    $log->model_id,
                    $log->getAfectado(),
                    $log->descripcion ?? $log->observacion ?? '-',
                    $log->ip_address ?? '-',
                    $log->user_agent ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Auditoria $auditoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auditoria $auditoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auditoria $auditoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auditoria $auditoria)
    {
        //
    }
}
