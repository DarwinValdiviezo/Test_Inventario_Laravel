<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ClienteRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rol de cliente si no existe
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);
        
        // Crear permisos básicos para clientes
        $permissions = [
            'ver-perfil',
            'editar-perfil',
            'ver-facturas-propias',
            'ver-productos',
            'realizar-compras',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Asignar permisos al rol de cliente
        $clienteRole->givePermissionTo($permissions);
        
        $this->command->info('Rol de cliente creado con permisos básicos.');
    }
} 