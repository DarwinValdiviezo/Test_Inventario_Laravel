<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación personalizada anti-XSS y anti-SQLi
        $peligroso = false;
        $mensaje = null;
        $patrones = [
            '/<script/i',
            '/\b(select|insert|update|delete|drop|truncate|union|--|#|;|\*|\bor\b|\band\b)\b/i',
        ];
        foreach (['name', 'email', 'password'] as $campo) {
            foreach ($patrones as $patron) {
                if (preg_match($patron, $request->input($campo, ''))) {
                    $peligroso = true;
                    $mensaje = 'Hey, estás intentando un ataque script o SQL, no está permitido.';
                    break 2;
                }
            }
        }
        if ($peligroso) {
            return redirect()->back()->withInput($request->except('password','password_confirmation'))
                ->withErrors(['danger' => $mensaje]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
