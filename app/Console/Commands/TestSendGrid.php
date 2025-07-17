<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Models\Factura;

class TestSendGrid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:sendgrid {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar envío de email con SendGrid API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("🔍 Probando SendGrid API para: {$email}");

        try {
            // Buscar una factura de prueba o crear una
            $factura = Factura::where('estado', 'activa')->first();
            
            if (!$factura) {
                $this->warn("⚠️ No hay facturas activas. Creando una de prueba...");
                
                // Crear factura de prueba
                $factura = new Factura();
                $factura->numero_factura = '001-001-000000001';
                $factura->cliente_id = 1;
                $factura->subtotal = 100.00;
                $factura->iva = 12.00;
                $factura->total = 112.00;
                $factura->estado = 'activa';
                $factura->usuario_id = 1;
                $factura->save();
                
                $this->info("✅ Factura de prueba creada con ID: {$factura->id}");
            } else {
                $this->info("✅ Usando factura existente: #{$factura->numero_factura}");
            }

            // Usar EmailService (SendGrid API directa)
            $emailService = new EmailService();
            $resultado = $emailService->enviarFactura(
                $factura,
                $email,
                "Prueba SendGrid API - " . now()->format('d/m/Y H:i:s'),
                "Esta es una prueba de envío usando SendGrid API directa.\n\nSi recibes este email, el sistema está funcionando correctamente."
            );
            
            if ($resultado) {
                $this->info("✅ Email enviado exitosamente usando SendGrid API!");
                $this->info("📧 Revisa tu bandeja de entrada en: {$email}");
                $this->info("📧 También revisa la carpeta de spam");
            } else {
                $this->error("❌ Error al enviar email con SendGrid API");
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("❌ Error al enviar email: " . $e->getMessage());
            $this->error("📋 Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
} 