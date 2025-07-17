<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Models\Factura;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {factura_id} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar envío de email de factura';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $facturaId = $this->argument('factura_id');
        $email = $this->argument('email');

        $this->info("Probando envío de email para factura #{$facturaId} a {$email}");

        try {
            // Buscar la factura
            $factura = Factura::with(['cliente', 'usuario', 'detalles.producto'])->find($facturaId);
            
            if (!$factura) {
                $this->error("Factura #{$facturaId} no encontrada");
                return 1;
            }

            $this->info("Factura encontrada: #{$factura->getNumeroFormateado()}");
            $this->info("Cliente: {$factura->cliente->nombre}");
            $this->info("Total: $" . number_format($factura->total, 2));

            // Verificar configuración de email
            $emailService = new EmailService();
            $config = $emailService->verificarConfiguracion();
            
            $this->info("Configuración de email:");
            foreach ($config as $key => $value) {
                $this->line("  {$key}: {$value}");
            }

            // Enviar email
            $asunto = 'Prueba - Factura #' . $factura->getNumeroFormateado() . ' - SowarTech';
            $mensaje = "Esta es una prueba del sistema de envío de facturas.\n\nFactura #{$factura->getNumeroFormateado()} por $" . number_format($factura->total, 2) . ".\n\nSaludos cordiales,\nEquipo de SowarTech";

            $this->info("Enviando email...");
            
            $enviado = $emailService->enviarFactura($factura, $email, $asunto, $mensaje);
            
            if ($enviado) {
                $this->info("✅ Email enviado exitosamente a {$email}");
            } else {
                $this->error("❌ Error al enviar email a {$email}");
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
} 