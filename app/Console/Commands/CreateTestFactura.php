<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;

class CreateTestFactura extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-factura';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear una factura de prueba para testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creando factura de prueba...');

        try {
            // Buscar o crear cliente
            $cliente = Cliente::first();
            if (!$cliente) {
                $this->info('No hay clientes. Creando uno...');
                $cliente = Cliente::create([
                    'nombre' => 'Cliente Prueba',
                    'email' => 'cliente@prueba.com',
                    'telefono' => '0999999999'
                ]);
                $this->info("Cliente creado con ID: {$cliente->id}");
            } else {
                $this->info("Usando cliente existente: {$cliente->nombre}");
            }

            // Buscar o crear producto
            $producto = Producto::first();
            if (!$producto) {
                $this->info('No hay productos. Creando uno...');
                $producto = Producto::create([
                    'nombre' => 'Producto Prueba',
                    'precio' => 100,
                    'stock' => 10,
                    'descripcion' => 'Producto de prueba para testing'
                ]);
                $this->info("Producto creado con ID: {$producto->id}");
            } else {
                $this->info("Usando producto existente: {$producto->nombre}");
            }

            // Crear factura
            $factura = Factura::create([
                'cliente_id' => $cliente->id,
                'usuario_id' => 1,
                'subtotal' => 100,
                'iva' => 12,
                'total' => 112,
                'estado_emision' => 'EMITIDA',
                'fecha_emision_email' => now()
            ]);

            // Crear detalle
            $factura->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => 1,
                'precio_unitario' => 100,
                'subtotal' => 100
            ]);

            $this->info("✅ Factura de prueba creada con ID: {$factura->id}");
            $this->info("Cliente: {$cliente->nombre}");
            $this->info("Producto: {$producto->nombre}");
            $this->info("Total: $" . number_format($factura->total, 2));

            return 0;

        } catch (\Exception $e) {
            $this->error("Error creando factura de prueba: " . $e->getMessage());
            return 1;
        }
    }
} 