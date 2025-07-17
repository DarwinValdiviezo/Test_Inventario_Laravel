<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            $errorMessage = null;
            // Si está eliminado (soft delete)
            if ($user->deleted_at) {
                $errorMessage = 'Su cuenta ha sido eliminada. Contacte soporte si es un error.';
            }
            // Si está pendiente de eliminación
            elseif ($user->pending_delete_at) {
                $fechaEliminacion = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(3);
                $ahora = \Carbon\Carbon::now();
                $diferencia = $fechaEliminacion->diff($ahora);
                
                $dias = $diferencia->days;
                $horas = $diferencia->h;
                
                // Verificar si es una ruta del perfil
                $currentPath = $request->path();
                $isProfilePath = str_starts_with($currentPath, 'profile') || $currentPath === 'profile';
                
                // Debug: Log para ver qué está pasando
                \Log::info('CheckUserStatus Debug', [
                    'user_id' => $user->id,
                    'pending_delete_at' => $user->pending_delete_at,
                    'current_path' => $request->path(),
                    'is_profile_path' => $isProfilePath,
                    'request_method' => $request->method(),
                    'dias' => $dias,
                    'horas' => $horas
                ]);
                
                if (!$isProfilePath) {
                    // En lugar de cerrar sesión, redirigir al perfil con un mensaje
                    $mensajeTiempo = '';
                    if ($dias > 0) {
                        $mensajeTiempo = $dias . ' día(s)';
                        if ($horas > 0) {
                            $mensajeTiempo .= ' y ' . $horas . ' hora(s)';
                        }
                    } else {
                        $mensajeTiempo = $horas . ' hora(s)';
                    }
                    
                    return redirect()->route('profile.edit')->with('warning', 'Su cuenta está en proceso de eliminación. Se eliminará definitivamente en ' . $mensajeTiempo . '. Solo puede acceder a su perfil para cancelar la eliminación.');
                }
            }
            // Si está inactivo
            elseif ($user->estado === 'inactivo') {
                Auth::logout();
                Session::flush();
                $mensaje = 'Su cuenta ha sido suspendida.';
                if ($user->observacion) {
                    $mensaje .= ' Razón: ' . $user->observacion;
                }
                return redirect('/login')->with('error', $mensaje);
            }
            // Si hay un mensaje de error, cerrar sesión y mostrar mensaje
            if ($errorMessage) {
                Auth::logout();
                Session::flush();
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => $errorMessage,
                        'redirect' => '/login'
                    ], 401);
                }
                return redirect('/login')->with('session_expired', $errorMessage);
            }
        }
        return $next($request);
    }
} 