<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;
use App\Services\FacturaSRIService;

class TestFirmaDigital extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:firma-digital {factura_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la funcionalidad de firma digital y QR para facturas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $facturaId = $this->argument('factura_id');
        
        if ($facturaId) {
            $factura = Factura::find($facturaId);
            if (!$factura) {
                $this->error("Factura #{$facturaId} no encontrada");
                return 1;
            }
            $this->probarFactura($factura);
        } else {
            $facturas = Factura::take(5)->get();
            if ($facturas->isEmpty()) {
                $this->error("No hay facturas en el sistema");
                return 1;
            }
            
            $this->info("Probando firma digital y QR para las primeras 5 facturas:");
            foreach ($facturas as $factura) {
                $this->probarFactura($factura);
                $this->line('');
            }
        }
        
        return 0;
    }
    
    private function probarFactura(Factura $factura)
    {
        $service = new FacturaSRIService();
        
        $this->info("=== Factura #{$factura->id} ===");
        $this->line("Cliente: " . ($factura->cliente->nombre ?? 'N/A'));
        $this->line("Total: $" . number_format($factura->total, 2));
        $this->line("Emisor: " . ($factura->usuario->name ?? 'N/A'));
        
        // Generar firma digital
        $firmaDigital = $service->generarFirmaDigital($factura);
        $this->line("Firma Digital: " . substr($firmaDigital, 0, 50) . "...");
        
        // Verificar firma digital
        $esValida = $service->verificarFirmaDigital($factura, $firmaDigital);
        $this->line("Firma Válida: " . ($esValida ? "✅ SÍ" : "❌ NO"));
        
        // Generar contenido QR
        $contenidoQR = $service->generarContenidoQR($factura);
        $this->line("Contenido QR: " . substr($contenidoQR, 0, 100) . "...");
        
        // Generar imagen QR
        $imagenQR = $service->generarImagenQR($factura);
        $this->line("Imagen QR: " . substr($imagenQR, 0, 50) . "...");
        
        // Verificar permisos
        $this->line("Permisos:");
        $this->line("  - Puede editar: " . (auth()->user() ? (auth()->user()->can('update', $factura) ? "✅ SÍ" : "❌ NO") : "N/A"));
        $this->line("  - Puede anular: " . (auth()->user() ? (auth()->user()->can('delete', $factura) ? "✅ SÍ" : "❌ NO") : "N/A"));
        $this->line("  - Puede eliminar: " . (auth()->user() ? (auth()->user()->can('forceDelete', $factura) ? "✅ SÍ" : "❌ NO") : "N/A"));
    }
} 