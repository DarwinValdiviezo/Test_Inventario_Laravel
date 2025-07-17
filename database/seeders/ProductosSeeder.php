<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronica = Categoria::where('nombre', 'Electrónicos')->first();
        $accesorios = Categoria::where('nombre', 'Accesorios')->first();
        $perifericos = Categoria::where('nombre', 'Periféricos')->first();

        Producto::create([
            'nombre' => 'Laptop HP',
            'descripcion' => 'Laptop HP 15 pulgadas, 8GB RAM, 256GB SSD',
            'categoria_id' => $electronica->id,
            'stock' => 10,
            'precio' => 750.00,
        ]);
        Producto::create([
            'nombre' => 'Mouse Logitech',
            'descripcion' => 'Mouse inalámbrico Logitech M185',
            'categoria_id' => $perifericos->id,
            'stock' => 50,
            'precio' => 20.00,
        ]);
        Producto::create([
            'nombre' => 'Monitor Samsung',
            'descripcion' => 'Monitor Samsung 24 pulgadas Full HD',
            'categoria_id' => $electronica->id,
            'stock' => 15,
            'precio' => 180.00,
        ]);
        Producto::create([
            'nombre' => 'Teclado Mecánico',
            'descripcion' => 'Teclado mecánico RGB con switches blue',
            'categoria_id' => $perifericos->id,
            'stock' => 25,
            'precio' => 120.00,
        ]);
        Producto::create([
            'nombre' => 'Cable HDMI',
            'descripcion' => 'Cable HDMI 2.0 de alta velocidad',
            'categoria_id' => $accesorios->id,
            'stock' => 100,
            'precio' => 8.50,
        ]);
        Producto::create([
            'nombre' => 'Disco Duro Externo',
            'descripcion' => 'Disco duro externo 1TB USB 3.0',
            'categoria_id' => $accesorios->id,
            'stock' => 8,
            'precio' => 65.00,
        ]);
    }
}
