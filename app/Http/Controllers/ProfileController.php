<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Información de perfil actualizada correctamente.');
    }

    /**
     * Delete the user's account (eliminación diferida).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $validator = \Validator::make($request->all(), [
            'password' => ['required', 'current_password'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return Redirect::route('profile.edit')
                ->withErrors($validator, 'userDeletion')
                ->withInput();
        }

        $user = $request->user();

        // Marcar para eliminación diferida (3 días)
        $user->pending_delete_at = now();
        $user->save();

        // Cerrar sesión
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/login')->with('warning', 'Su cuenta ha sido marcada para eliminación. Se eliminará definitivamente en 3 días. Puede cancelar esta acción iniciando sesión antes de ese plazo.');
    }

    /**
     * Cancelar la eliminación de cuenta.
     */
    public function cancelarBorradoCuenta(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Solo permitir cancelar si está pendiente de eliminación
        if (!$user->pending_delete_at) {
            return Redirect::route('profile.edit')->with('error', 'No hay una eliminación pendiente para cancelar.');
        }

        // Cancelar eliminación
        $user->pending_delete_at = null;
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Eliminación de cuenta cancelada exitosamente. Su cuenta está activa nuevamente.');
    }
}
