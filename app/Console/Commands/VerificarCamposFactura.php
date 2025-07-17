<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class VerificarCamposFactura extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factura:verificar-campos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica qué campos existen en la tabla facturas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Verificando campos de la tabla facturas...");
        
        $columns = Schema::getColumnListing('facturas');
        
        $this->line("Campos existentes:");
        foreach ($columns as $column) {
            $this->line("  - {$column}");
        }
        
        // Verificar campos SRI específicos
        $camposSRI = [
            'numero_secuencial',
            'cua',
            'fecha_emision',
            'hora_emision',
            'ambiente',
            'tipo_emision',
            'tipo_documento',
            'mensaje_autorizacion',
            'firma_digital',
            'contenido_qr',
            'imagen_qr',
            'forma_pago'
        ];
        
        $this->line("\nCampos SRI:");
        foreach ($camposSRI as $campo) {
            $existe = in_array($campo, $columns);
            $status = $existe ? "✅ Existe" : "❌ Falta";
            $this->line("  - {$campo}: {$status}");
        }
        
        return 0;
    }
} 