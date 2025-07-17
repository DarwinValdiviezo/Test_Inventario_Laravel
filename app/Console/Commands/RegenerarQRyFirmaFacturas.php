<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;

class RegenerarQRyFirmaFacturas extends Command
{
    protected $signature = 'facturas:regenerar-qr-firma';
    protected $description = 'Regenera el QR y la firma digital de todas las facturas';

    public function handle()
    {
        $this->info('Regenerando QR y firma digital de todas las facturas...');
        $total = 0;
        Factura::with(['cliente', 'detalles'])->chunk(50, function ($facturas) use (&$total) {
            foreach ($facturas as $factura) {
                // Generar QR y firma
                $factura->generarFirmaYQR();
                
                // Establecer estados iniciales si no están definidos
                if (empty($factura->estado_firma)) {
                    $factura->estado_firma = 'PENDIENTE';
                }
                if (empty($factura->estado_emision)) {
                    $factura->estado_emision = 'PENDIENTE';
                }
                
                $factura->save();
                $total++;
                $this->line("Factura #{$factura->id} actualizada");
            }
        });
        $this->info("Listo. Facturas actualizadas: $total");
        return 0;
    }
} 