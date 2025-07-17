<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Verificar si el usuario ya tiene rol de cliente
        if ($user->hasRole('cliente')) {
            return; // Ya es cliente, no crear duplicado
        }
        
        // Verificar si ya existe un cliente con ese email
        $existingCliente = Cliente::where('email', $user->email)->first();
        if ($existingCliente) {
            return; // Ya existe un cliente con ese email
        }
        
        // Crear cliente automáticamente para usuarios que se registran
        Cliente::create([
            'nombre' => $user->name,
            'email' => $user->email,
            'password' => $user->password, // Ya está hasheada
            'telefono' => null,
            'direccion' => null,
            'estado' => 'activo',
            'user_id' => $user->id,
            'created_by' => $user->id,
        ]);
        
        // Asignar rol de cliente
        $user->assignRole('cliente');
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
