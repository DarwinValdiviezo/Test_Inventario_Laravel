<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Producto $producto): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Producto $producto): Response|bool
    {
        if ($user->hasRole(['Administrador', 'Bodega']) || $producto->created_by === $user->id) {
            return true;
        }
        return Response::deny('No tienes permiso para actualizar este producto.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Producto $producto): Response|bool
    {
        if ($user->hasRole(['Administrador', 'Bodega']) || $producto->created_by === $user->id) {
            return true;
        }
        return Response::deny('No tienes permiso para eliminar este producto.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Producto $producto): Response|bool
    {
        if ($user->hasRole(['Administrador', 'Bodega']) || $producto->created_by === $user->id) {
            return true;
        }
        return Response::deny('No tienes permiso para restaurar este producto.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Producto $producto): Response|bool
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return Response::deny('Solo el administrador puede eliminar permanentemente este producto.');
    }
}
