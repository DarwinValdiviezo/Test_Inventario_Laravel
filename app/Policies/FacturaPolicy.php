<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FacturaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Ventas']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Factura $factura): bool
    {
        return $user->hasRole(['Administrador', 'Ventas']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Administrador', 'Ventas']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Factura $factura): Response|bool
    {
        if ($user->hasRole('Administrador') || $factura->usuario_id === $user->id) {
            return true;
        }
        return Response::deny('No tienes permiso para actualizar esta factura.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Factura $factura): Response|bool
    {
        if ($user->hasRole('Administrador') || $factura->usuario_id === $user->id) {
            return true;
        }
        return Response::deny('No tienes permiso para anular esta factura.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Factura $factura): Response|bool
    {
        if ($user->hasRole('Administrador') || $factura->usuario_id === $user->id) {
            return true;
        }
        return Response::deny('No tienes permiso para restaurar esta factura.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Factura $factura): Response|bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return Response::deny('Solo el administrador puede eliminar permanentemente esta factura.');
    }
}
