<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleApiController extends Controller
{
    /**
     * Obtener todos los roles
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
                    'message' => 'Solo los administradores pueden ver roles.'
                ], 403);
            }

            $roles = Role::with(['permissions'])->get();

            $data = $roles->map(function ($role) {
                return [
                    'id' => (int) $role->id,
                    'name' => (string) $role->name,
                    'guard_name' => (string) $role->guard_name,
                    'permissions_count' => (int) $role->permissions->count(),
                    'users_count' => (int) $role->users->count(),
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => (int) $permission->id,
                            'name' => (string) $permission->name,
                            'guard_name' => (string) $permission->guard_name
                        ];
                    })->toArray(),
                    'created_at' => $role->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $role->updated_at->format('Y-m-d H:i:s')
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Roles obtenidos exitosamente',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API roles index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Crear un nuevo rol
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
            if (!$user->hasRole('Administrador')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los administradores pueden crear roles.'
                ], 403);
            }

            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'guard_name' => 'required|string|max:255',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name
            ]);

            // Asignar permisos si se proporcionan
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente',
                'data' => $role
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en API roles store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Eliminar un rol
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
            if (!$user->hasRole('Administrador')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los administradores pueden eliminar roles.'
                ], 403);
            }

            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            // Verificar que no sea un rol del sistema
            $rolesDelSistema = ['Administrador', 'Secretario', 'Bodega', 'Ventas', 'cliente'];
            if (in_array($role->name, $rolesDelSistema)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un rol del sistema.'
                ], 400);
            }

            // Verificar que no tenga usuarios asignados
            if ($role->users->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar un rol que tiene usuarios asignados.'
                ], 400);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en API roles destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 