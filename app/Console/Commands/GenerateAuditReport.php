<?php

namespace App\Console\Commands;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateAuditReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:report 
                            {--user= : Filtrar por usuario específico}
                            {--action= : Filtrar por acción específica}
                            {--model= : Filtrar por modelo específico}
                            {--start-date= : Fecha de inicio (YYYY-MM-DD)}
                            {--end-date= : Fecha de fin (YYYY-MM-DD)}
                            {--format=csv : Formato de salida (csv, json, html)}
                            {--output= : Archivo de salida}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar reporte de auditoría con filtros opcionales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generando reporte de auditoría...');

        $query = Auditoria::query();

        // Aplicar filtros
        if ($this->option('user')) {
            $query->where('user_id', $this->option('user'));
        }

        if ($this->option('action')) {
            $query->where('action', $this->option('action'));
        }

        if ($this->option('model')) {
            $query->where('model_type', $this->option('model'));
        }

        if ($this->option('start-date')) {
            $query->whereDate('created_at', '>=', $this->option('start-date'));
        }

        if ($this->option('end-date')) {
            $query->whereDate('created_at', '<=', $this->option('end-date'));
        }

        $logs = $query->with('user')->orderBy('created_at', 'desc')->get();

        if ($logs->isEmpty()) {
            $this->warn('No se encontraron registros de auditoría con los filtros especificados.');
            return 1;
        }

        $format = $this->option('format');
        $output = $this->option('output');

        switch ($format) {
            case 'csv':
                $this->generateCsvReport($logs, $output);
                break;
            case 'json':
                $this->generateJsonReport($logs, $output);
                break;
            case 'html':
                $this->generateHtmlReport($logs, $output);
                break;
            default:
                $this->error('Formato no soportado. Use: csv, json, html');
                return 1;
        }

        $this->info("Reporte generado exitosamente con {$logs->count()} registros.");
        return 0;
    }

    /**
     * Generar reporte CSV
     */
    private function generateCsvReport($logs, $output = null)
    {
        $filename = $output ?: 'audit_report_' . date('Y-m-d_H-i-s') . '.csv';
        $file = fopen($filename, 'w');

        // Headers
        fputcsv($file, [
            'ID', 'Fecha', 'Usuario', 'Email', 'Acción', 'Modelo', 'ID Modelo',
            'Afectado', 'Descripción', 'IP Address', 'User Agent'
        ]);

        foreach ($logs as $log) {
            fputcsv($file, [
                $log->id,
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
        $this->info("Reporte CSV guardado en: {$filename}");
    }

    /**
     * Generar reporte JSON
     */
    private function generateJsonReport($logs, $output = null)
    {
        $filename = $output ?: 'audit_report_' . date('Y-m-d_H-i-s') . '.json';
        
        $data = [
            'generated_at' => now()->toISOString(),
            'total_records' => $logs->count(),
            'filters_applied' => [
                'user' => $this->option('user'),
                'action' => $this->option('action'),
                'model' => $this->option('model'),
                'start_date' => $this->option('start-date'),
                'end_date' => $this->option('end-date'),
            ],
            'records' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'created_at' => $log->created_at->toISOString(),
                    'user' => $log->user ? [
                        'id' => $log->user->id,
                        'name' => $log->user->name,
                        'email' => $log->user->email,
                    ] : null,
                    'action' => $log->action,
                    'model_type' => $log->model_type,
                    'model_id' => $log->model_id,
                    'affected' => $log->getAfectado(),
                    'description' => $log->descripcion ?? $log->observacion,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'old_values' => $log->old_values ? json_decode($log->old_values, true) : null,
                    'new_values' => $log->new_values ? json_decode($log->new_values, true) : null,
                ];
            })
        ];

        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("Reporte JSON guardado en: {$filename}");
    }

    /**
     * Generar reporte HTML
     */
    private function generateHtmlReport($logs, $output = null)
    {
        $filename = $output ?: 'audit_report_' . date('Y-m-d_H-i-s') . '.html';
        
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Auditoría</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .header { background-color: #4CAF50; color: white; padding: 15px; margin-bottom: 20px; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .stat-card { background-color: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Auditoría</h1>
        <p>Generado el: ' . now()->format('d/m/Y H:i:s') . '</p>
    </div>';

        // Estadísticas
        $stats = [
            'total' => $logs->count(),
            'users' => $logs->pluck('user_id')->unique()->count(),
            'actions' => $logs->groupBy('action')->map->count(),
            'models' => $logs->groupBy('model_type')->map->count(),
        ];

        $html .= '<div class="stats">
            <div class="stat-card">
                <h3>' . $stats['total'] . '</h3>
                <p>Total Registros</p>
            </div>
            <div class="stat-card">
                <h3>' . $stats['users'] . '</h3>
                <p>Usuarios Únicos</p>
            </div>
            <div class="stat-card">
                <h3>' . $stats['actions']->count() . '</h3>
                <p>Tipos de Acciones</p>
            </div>
            <div class="stat-card">
                <h3>' . $stats['models']->count() . '</h3>
                <p>Modelos Afectados</p>
            </div>
        </div>';

        $html .= '<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Modelo</th>
                    <th>Afectado</th>
                    <th>Descripción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($logs as $log) {
            $actionClass = [
                'create' => 'badge-success',
                'update' => 'badge-warning',
                'delete' => 'badge-danger'
            ][$log->action] ?? 'badge-secondary';

            $html .= '<tr>
                <td>' . $log->id . '</td>
                <td>' . $log->created_at->format('d/m/Y H:i:s') . '</td>
                <td>' . ($log->user ? $log->user->name : 'Desconocido') . '</td>
                <td><span class="badge ' . $actionClass . '">' . ucfirst($log->action) . '</span></td>
                <td>' . class_basename($log->model_type) . '</td>
                <td>' . $log->getAfectado() . '</td>
                <td>' . ($log->descripcion ?? $log->observacion ?? '-') . '</td>
                <td>' . ($log->ip_address ?? '-') . '</td>
            </tr>';
        }

        $html .= '</tbody></table></body></html>';

        file_put_contents($filename, $html);
        $this->info("Reporte HTML guardado en: {$filename}");
    }
} 