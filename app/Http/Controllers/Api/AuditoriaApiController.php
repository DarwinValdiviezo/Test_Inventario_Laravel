<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditoriaApiController extends Controller
{
    /**
     * Obtener todas las auditorías
     */
    public function index(Request $request): JsonResponse
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
            if (!$user->hasRole('Administrador')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los administradores pueden ver auditorías.'
                ], 403);
            }

            $query = Auditoria::with(['user']);

            // Filtros
            if ($request->filled('action')) {
                $query->where('action', $request->input('action'));
            }

            if ($request->filled('model_type')) {
                $query->where('model_type', $request->input('model_type'));
            }

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
            }

            $auditorias = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Auditorías obtenidas exitosamente',
                'data' => $auditorias
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API auditorias index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Exportar auditorías
     */
    public function export(Request $request): JsonResponse
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
            if (!$user->hasRole('Administrador')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los administradores pueden exportar auditorías.'
                ], 403);
            }

            $query = Auditoria::with(['user']);

            // Aplicar filtros si existen
            if ($request->filled('action')) {
                $query->where('action', $request->input('action'));
            }

            if ($request->filled('model_type')) {
                $query->where('model_type', $request->input('model_type'));
            }

            if ($request->filled('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
            }

            $auditorias = $query->orderBy('created_at', 'desc')->get();

            $data = $auditorias->map(function ($auditoria) {
                return [
                    'id' => $auditoria->id,
                    'action' => $auditoria->action,
                    'model_type' => $auditoria->model_type,
                    'model_id' => $auditoria->model_id,
                    'description' => $auditoria->description,
                    'observacion' => $auditoria->observacion,
                    'user' => $auditoria->user ? [
                        'id' => $auditoria->user->id,
                        'name' => $auditoria->user->name,
                        'email' => $auditoria->user->email
                    ] : null,
                    'ip_address' => $auditoria->ip_address,
                    'user_agent' => $auditoria->user_agent,
                    'created_at' => $auditoria->created_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Auditorías exportadas exitosamente',
                'data' => $data,
                'total' => $data->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API auditorias export: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 