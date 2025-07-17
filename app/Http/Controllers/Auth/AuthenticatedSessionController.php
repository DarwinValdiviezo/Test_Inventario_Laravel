<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Si el usuario está inactivo, no permitir login y mostrar motivo
        if ($user && $user->estado === 'inactivo') {
            Auth::logout();
            return redirect()->route('login')->with([
                'inactiva' => true,
                'motivo' => $user->motivo_suspension ?? 'Cuenta inactiva por el administrador.'
            ]);
        }
        
        // Si el usuario está pendiente de eliminación, redirigir al perfil
        if ($user && $user->pending_delete_at) {
            $fechaEliminacion = \Carbon\Carbon::parse($user->pending_delete_at)->addDays(3);
            $ahora = \Carbon\Carbon::now();
            $diferencia = $fechaEliminacion->diff($ahora);
            
            $dias = $diferencia->days;
            $horas = $diferencia->h;
            
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

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
