<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClienteApiController extends Controller
{
    /**
     * Obtener todos los clientes
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
            $rolesPermitidos = ['Administrador', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver clientes.'
                ], 403);
            }

            $query = Cliente::with(['user']);

            // Filtros
            if ($request->filled('buscar')) {
                $buscar = $request->input('buscar');
                $query->where(function($q) use ($buscar) {
                    $q->where('nombre', 'like', "%$buscar%")
                      ->orWhere('email', 'like', "%$buscar%")
                      ->orWhere('telefono', 'like', "%$buscar%");
                });
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->input('estado'));
            }

            // Si piden eliminados, mostrar solo eliminados
            if ($request->has('eliminados') && $request->input('eliminados') == 1) {
                $query = $query->onlyTrashed();
            } else {
                $query = $query->whereNull('deleted_at');
            }

            $clientes = $query->orderBy('nombre')->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Clientes obtenidos exitosamente',
                'data' => $clientes
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API clientes index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener un cliente específico
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
            $rolesPermitidos = ['Administrador', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para ver clientes.'
                ], 403);
            }

            $cliente = Cliente::with(['user', 'facturas'])->find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $data = [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'estado' => $cliente->estado,
                'user' => $cliente->user ? [
                    'id' => $cliente->user->id,
                    'name' => $cliente->user->name,
                    'email' => $cliente->user->email,
                    'estado' => $cliente->user->estado
                ] : null,
                'facturas_count' => $cliente->facturas()->whereNull('deleted_at')->count(),
                'created_at' => $cliente->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $cliente->updated_at->format('Y-m-d H:i:s')
            ];

            return response()->json([
                'success' => true,
                'message' => 'Cliente obtenido exitosamente',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API clientes show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Crear un nuevo cliente
     */
    public function store(Request $request): JsonResponse
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
            $rolesPermitidos = ['Administrador', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para crear clientes.'
                ], 403);
            }

            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|unique:clientes,email',
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string|max:500',
                'password' => 'required|string|min:8'
            ]);

            $cliente = Cliente::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'password' => bcrypt($request->password),
                'estado' => 'activo',
                'created_by' => $user->id,
                'updated_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => $cliente
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en API clientes store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Actualizar un cliente
     */
    public function update(Request $request, $id): JsonResponse
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
            $rolesPermitidos = ['Administrador', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para actualizar clientes.'
                ], 403);
            }

            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:clientes,email,' . $id,
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string|max:500',
                'estado' => 'sometimes|required|in:activo,inactivo'
            ]);

            $cliente->update([
                'nombre' => $request->nombre ?? $cliente->nombre,
                'email' => $request->email ?? $cliente->email,
                'telefono' => $request->telefono ?? $cliente->telefono,
                'direccion' => $request->direccion ?? $cliente->direccion,
                'estado' => $request->estado ?? $cliente->estado,
                'updated_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
                'data' => $cliente
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API clientes update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Eliminar un cliente (soft delete)
     */
    public function destroy($id): JsonResponse
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
            $rolesPermitidos = ['Administrador', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar clientes.'
                ], 403);
            }

            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $cliente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API clientes destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Restaurar un cliente eliminado
     */
    public function restore($id): JsonResponse
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
            $rolesPermitidos = ['Administrador', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para restaurar clientes.'
                ], 403);
            }

            $cliente = Cliente::onlyTrashed()->find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $cliente->restore();

            return response()->json([
                'success' => true,
                'message' => 'Cliente restaurado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API clientes restore: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Eliminar permanentemente un cliente
     */
    public function forceDelete($id): JsonResponse
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
                    'message' => 'Solo los administradores pueden eliminar permanentemente clientes.'
                ], 403);
            }

            $cliente = Cliente::withTrashed()->find($id);

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            $cliente->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado permanentemente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API clientes forceDelete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 