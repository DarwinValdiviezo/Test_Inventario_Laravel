<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Login para la API móvil
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
                'device_name' => 'required|string|max:255',
            ], [
                'email.required' => 'El email es obligatorio',
                'email.email' => 'El formato del email no es válido',
                'password.required' => 'La contraseña es obligatoria',
                'device_name.required' => 'El nombre del dispositivo es obligatorio',
                'device_name.max' => 'El nombre del dispositivo no puede tener más de 255 caracteres'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            // Verificar si el usuario está activo
            if ($user->estado !== 'activo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu cuenta está inactiva. Contacta al administrador.'
                ], 403);
            }

            // Verificar contraseña
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            // Verificar que el usuario tenga roles válidos para la API
            $rolesPermitidos = ['Administrador', 'Bodega', 'Ventas', 'Secretario'];
            if (!$user->hasAnyRole($rolesPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para acceder a la API móvil'
                ], 403);
            }

            // Eliminar tokens anteriores del mismo dispositivo
            $user->tokens()->where('name', $request->device_name)->delete();

            // Crear nuevo token
            $token = $user->createToken($request->device_name)->plainTextToken;

            // Registrar en auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => $user->id,
                    'action' => 'login_api',
                    'model_type' => User::class,
                    'model_id' => $user->id,
                    'description' => 'Inicio de sesión en API móvil',
                    'observacion' => 'Dispositivo: ' . $request->device_name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'estado' => $user->estado,
                        'roles' => $user->roles->pluck('name')
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en login API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Logout para la API móvil
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 401);
            }

            // Registrar en auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => $user->id,
                    'action' => 'logout_api',
                    'model_type' => User::class,
                    'model_id' => $user->id,
                    'description' => 'Cierre de sesión en API móvil',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);
            }

            // Revocar el token actual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout exitoso'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en logout API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Información del usuario obtenida',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'estado' => $user->estado,
                        'roles' => $user->roles->pluck('name'),
                        'created_at' => $user->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en me API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cambiar contraseña desde la API
     */
    public function cambiarPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string'
            ], [
                'current_password.required' => 'La contraseña actual es obligatoria',
                'new_password.required' => 'La nueva contraseña es obligatoria',
                'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
                'new_password.confirmed' => 'Las contraseñas no coinciden',
                'new_password_confirmation.required' => 'Debe confirmar la nueva contraseña'
            ]);

            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 401);
            }

            // Verificar contraseña actual
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ], 422);
            }

            // Actualizar contraseña
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Registrar en auditoría
            if (class_exists('App\\Models\\Auditoria')) {
                \App\Models\Auditoria::create([
                    'user_id' => $user->id,
                    'action' => 'cambiar_password_api',
                    'model_type' => User::class,
                    'model_id' => $user->id,
                    'description' => 'Cambio de contraseña desde API móvil',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contraseña cambiada exitosamente'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en cambiar password API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
} 