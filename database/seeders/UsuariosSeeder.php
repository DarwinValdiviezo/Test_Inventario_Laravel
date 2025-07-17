<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'darwinrvaldiviezo@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Administrador');

        // Crear usuario secretario
        $secretario = User::create([
            'name' => 'Secretario',
            'email' => 'secretario@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $secretario->assignRole('Secretario');

        // Crear usuario bodega
        $bodega = User::create([
            'name' => 'Bodega',
            'email' => 'bodega@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $bodega->assignRole('Bodega');

        // Crear usuario ventas
        $ventas = User::create([
            'name' => 'Ventas',
            'email' => 'ventas@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $ventas->assignRole('Ventas');
    }
}
