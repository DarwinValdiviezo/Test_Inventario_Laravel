<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password'); // Contraseña por defecto para seeders
        
        Cliente::create([
            'nombre' => 'Juan Pérez',
            'email' => 'juanperez@gmail.com',
            'password' => $password,
            'telefono' => '0999999999',
            'direccion' => 'Av. Principal 123',
            'estado' => 'activo',
        ]);
        Cliente::create([
            'nombre' => 'María García',
            'email' => 'mariagarcia@gmail.com',
            'password' => $password,
            'telefono' => '0988888888',
            'direccion' => 'Calle Secundaria 456',
            'estado' => 'activo',
        ]);
        Cliente::create([
            'nombre' => 'Carlos López',
            'email' => 'carloslopez@gmail.com',
            'password' => $password,
            'telefono' => '0977777777',
            'direccion' => 'Av. Tercera 789',
            'estado' => 'inactivo',
        ]);
    }
}
