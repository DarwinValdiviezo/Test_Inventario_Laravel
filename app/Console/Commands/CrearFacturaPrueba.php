<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\FacturaDetalle;
use App\Services\FacturaSRIService;
use Illuminate\Support\Facades\Auth;

class CrearFacturaPrueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factura:crear-prueba';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una factura de prueba con datos SRI completos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Creando factura de prueba...");
        
        // Obtener o crear cliente de prueba
        $cliente = Cliente::first();
        if (!$cliente) {
            $this->error("No hay clientes en el sistema. Crea al menos un cliente primero.");
            return 1;
        }
        
        // Obtener productos
        $productos = Producto::where('stock', '>', 0)->take(3)->get();
        if ($productos->isEmpty()) {
            $this->error("No hay productos con stock disponible.");
            return 1;
        }
        
        // Obtener usuario
        $usuario = User::first();
        if (!$usuario) {
            $this->error("No hay usuarios en el sistema.");
            return 1;
        }
        
        // Crear factura
        $service = new FacturaSRIService();
        $datosSRI = $service->prepararDatosSRI(0); // Se calculará después
        
        $factura = Factura::create([
            'cliente_id' => $cliente->id,
            'usuario_id' => $usuario->id,
            'subtotal' => 0,
            'iva' => 0,
            'total' => 0,
            'estado' => 'activa',
            'forma_pago' => 'EFECTIVO',
            'numero_secuencial' => $datosSRI['numero_secuencial'],
            'cua' => $datosSRI['cua'],
            'fecha_emision' => $datosSRI['fecha_emision'],
            'hora_emision' => $datosSRI['hora_emision'],
            'ambiente' => $datosSRI['ambiente'],
            'tipo_emision' => $datosSRI['tipo_emision'],
            'tipo_documento' => $datosSRI['tipo_documento'],
            'mensaje_autorizacion' => $datosSRI['mensaje_autorizacion'],
        ]);
        
        // Crear detalles
        $subtotalTotal = 0;
        foreach ($productos as $producto) {
            $cantidad = rand(1, 3);
            $subtotal = $producto->precio * $cantidad;
            $subtotalTotal += $subtotal;
            
            FacturaDetalle::create([
                'factura_id' => $factura->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio,
                'subtotal' => $subtotal,
                'created_by' => $usuario->id,
            ]);
            
            // Actualizar stock
            $producto->stock -= $cantidad;
            $producto->save();
        }
        
        // Actualizar totales
        $totales = $service->calcularTotales($subtotalTotal);
        $factura->update([
            'subtotal' => $totales['subtotal'],
            'iva' => $totales['iva'],
            'total' => $totales['total'],
        ]);
        
        // Generar firma digital y QR
        $factura->generarFirmaYQR();
        
        $this->info("✅ Factura de prueba creada exitosamente!");
        $this->line("ID: {$factura->id}");
        $this->line("Cliente: {$cliente->nombre}");
        $this->line("Total: $" . number_format((float)
$factura->total, 2));
        $this->line("Secuencial: {$factura->numero_secuencial}");
        $this->line("CUA: {$factura->cua}");
        $this->line("Firma Digital: " . substr($factura->firma_digital, 0, 50) . "...");
        
        return 0;
    }
} 