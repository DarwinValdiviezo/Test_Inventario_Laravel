<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserApiController extends Controller
{
    /**
     * Listar usuarios (con filtros opcionales)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado. Token requerido.'], 401);
            }
            $user = Auth::user();
            if (!$user->hasRole('Administrador')) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para ver usuarios.'], 403);
            }

            $query = User::with('roles');
            if ($request->has('eliminados') && $request->input('eliminados') == 1) {
                $query = $query->onlyTrashed();
            } else {
                $query = $query->whereNull('deleted_at');
            }
            if ($request->filled('buscar')) {
                $buscar = $request->input('buscar');
                $query->where(function($q) use ($buscar) {
                    $q->where('name', 'like', "%$buscar%")
                      ->orWhere('email', 'like', "%$buscar%")
                      ->orWhereHas('roles', function($qr) use ($buscar) {
                          $qr->where('name', 'like', "%$buscar%") ;
                      });
                });
            }
            $orden = $request->input('orden', 'id');
            $direccion = $request->input('direccion', 'desc');
            $query->orderBy($orden, $direccion);
            $perPage = $request->input('per_page', 15);
            $users = $query->paginate($perPage);
            $users->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'estado' => $user->estado,
                    'roles' => $user->roles->pluck('name'),
                    'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : null,
                    'deleted_at' => $user->deleted_at ? $user->deleted_at->format('Y-m-d H:i:s') : null,
                ];
            });
            return response()->json([
                'success' => true,
                'message' => 'Usuarios obtenidos exitosamente',
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error en API usuarios index: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno del servidor', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Mostrar un usuario específico
     */
    public function show($id): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado. Token requerido.'], 401);
            }
            $user = Auth::user();
            if (!$user->hasRole('Administrador')) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para ver usuarios.'], 403);
            }
            $usuario = User::with('roles')->find($id);
            if (!$usuario) {
                return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            }
            $data = [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'estado' => $usuario->estado,
                'roles' => $usuario->roles->pluck('name'),
                'created_at' => $usuario->created_at ? $usuario->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $usuario->updated_at ? $usuario->updated_at->format('Y-m-d H:i:s') : null,
                'deleted_at' => $usuario->deleted_at ? $usuario->deleted_at->format('Y-m-d H:i:s') : null,
            ];
            return response()->json(['success' => true, 'message' => 'Usuario obtenido exitosamente', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error en API usuarios show: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno del servidor', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Crear usuario
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'estado' => 'required|in:activo,inactivo',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:roles,name',
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'estado' => $request->estado,
            ]);
            $user->syncRoles($request->roles);
            // Auditoría opcional
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'create',
                    'model_type' => User::class,
                    'model_id' => $user->id,
                    'description' => 'Usuario creado vía API',
                    'observacion' => 'API',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Usuario creado correctamente', 'data' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $usuario->id,
                'password' => 'nullable|string|min:8|confirmed',
                'estado' => 'required|in:activo,inactivo',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:roles,name',
            ]);
            $usuario->name = $request->name;
            $usuario->email = $request->email;
            if ($request->password) {
                $usuario->password = Hash::make($request->password);
            }
            $usuario->estado = $request->estado;
            $usuario->save();
            $usuario->syncRoles($request->roles);
            // Auditoría opcional
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'update',
                    'model_type' => User::class,
                    'model_id' => $usuario->id,
                    'description' => 'Usuario actualizado vía API',
                    'observacion' => 'API',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente', 'data' => $usuario]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
            $user = Auth::user();
            $password = $request->input('password');
            if (!$password || !Hash::check($password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $observacion = $request->input('observacion');
            if (!$observacion) {
                return response()->json(['success' => false, 'message' => 'El motivo es obligatorio.'], 422);
            }
            $old = $usuario->toArray();
            $usuario->delete();
            // Auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => $user->id,
                    'action' => 'delete',
                    'model_type' => User::class,
                    'model_id' => $usuario->id,
                    'old_values' => json_encode($old),
                    'new_values' => null,
                    'description' => 'Usuario eliminado (soft)',
                    'observacion' => $observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Restaurar usuario eliminado (soft delete)
     */
    public function restore(Request $request, $id): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
            $user = Auth::user();
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $usuario = User::onlyTrashed()->findOrFail($id);
            $usuario->restore();
            // Auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => $user->id,
                    'action' => 'restore',
                    'model_type' => User::class,
                    'model_id' => $usuario->id,
                    'old_values' => null,
                    'new_values' => json_encode($usuario->toArray()),
                    'description' => 'Usuario restaurado',
                    'observacion' => $request->observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Usuario restaurado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al restaurar usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Eliminar usuario definitivamente (force delete)
     */
    public function forceDelete(Request $request, $id): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
            }
            $user = Auth::user();
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $usuario = User::onlyTrashed()->findOrFail($id);
            $old = $usuario->toArray();
            $usuario->forceDelete();
            // Auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => $user->id,
                    'action' => 'forceDelete',
                    'model_type' => User::class,
                    'model_id' => $usuario->id,
                    'old_values' => json_encode($old),
                    'new_values' => null,
                    'description' => 'Usuario eliminado permanentemente',
                    'observacion' => $request->observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Usuario eliminado permanentemente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar permanentemente el usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleEstado($id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
            $usuario->save();
            // Auditoría opcional
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'toggleEstado',
                    'model_type' => User::class,
                    'model_id' => $usuario->id,
                    'description' => 'Estado de usuario cambiado vía API',
                    'observacion' => 'API',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Estado del usuario actualizado', 'data' => $usuario]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cambiar estado', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Activar usuario
     */
    public function activarUsuario(Request $request, $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);
            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $usuario->estado = 'activo';
            $usuario->observacion = $request->observacion;
            $usuario->save();
            // Auditoría opcional
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'activar',
                    'model_type' => User::class,
                    'model_id' => $usuario->id,
                    'description' => 'Usuario activado vía API',
                    'observacion' => $request->observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Usuario activado correctamente', 'data' => $usuario]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al activar usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Desactivar usuario
     */
    public function desactivarUsuario(Request $request, $id): JsonResponse
    {
        try {
            $usuario = User::findOrFail($id);
            $request->validate([
                'password' => 'required|string',
                'observacion' => 'required|string',
            ]);
            if (!Hash::check($request->password, Auth::user()->password)) {
                return response()->json(['success' => false, 'message' => 'Contraseña incorrecta.'], 403);
            }
            $usuario->estado = 'inactivo';
            $usuario->observacion = $request->observacion;
            $usuario->save();
            // Auditoría opcional
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'desactivar',
                    'model_type' => User::class,
                    'model_id' => $usuario->id,
                    'description' => 'Usuario desactivado vía API',
                    'observacion' => $request->observacion,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Usuario desactivado correctamente', 'data' => $usuario]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al desactivar usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    /**
     * Crear token de acceso manual para usuario
     */
    public function crearTokenAcceso(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'usuario_id' => 'required|exists:users,id',
                'token_name' => 'required|string|max:255',
            ]);
            $user = User::findOrFail($request->usuario_id);
            $token = $user->createToken($request->token_name)->plainTextToken;
            // Auditoría opcional
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => Auth::id(),
                    'action' => 'crear_token',
                    'model_type' => User::class,
                    'model_id' => $user->id,
                    'description' => 'Token creado vía API',
                    'observacion' => 'Nombre del token: ' . $request->token_name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Token creado correctamente', 'token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear token', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }
} 