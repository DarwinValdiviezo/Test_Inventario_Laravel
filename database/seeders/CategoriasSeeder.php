<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create([
            'nombre' => 'Electrónicos',
            'descripcion' => 'Productos electrónicos y tecnología',
            'color' => '#007bff',
            'activo' => true,
        ]);
        Categoria::create([
            'nombre' => 'Accesorios',
            'descripcion' => 'Accesorios para computadoras',
            'color' => '#28a745',
            'activo' => true,
        ]);
        Categoria::create([
            'nombre' => 'Periféricos',
            'descripcion' => 'Periféricos de computadora',
            'color' => '#ffc107',
            'activo' => true,
        ]);
        Categoria::create([
            'nombre' => 'Software',
            'descripcion' => 'Software y licencias',
            'color' => '#dc3545',
            'activo' => true,
        ]);
    }
}
