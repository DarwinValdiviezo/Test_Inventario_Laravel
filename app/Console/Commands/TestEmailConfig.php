<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Models\Factura;

class TestEmailConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-config {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar configuración de email con SendGrid API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("🔍 Probando configuración de email para: {$email}");

        try {
            // Verificar configuración
            $emailService = new EmailService();
            $config = $emailService->verificarConfiguracion();
            
            $this->info("📋 Configuración actual:");
            foreach ($config as $key => $value) {
                $this->line("  {$key}: {$value}");
            }

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

            // Enviar email de prueba usando EmailService
            $this->info("🚀 Enviando email de prueba con SendGrid API...");
            
            $resultado = $emailService->enviarFactura(
                $factura,
                $email,
                "Prueba de Configuración - " . now()->format('d/m/Y H:i:s'),
                "Esta es una prueba de configuración usando SendGrid API.\n\nSi recibes este email, la configuración está funcionando correctamente."
            );
            
            if ($resultado) {
                $this->info("✅ Email de prueba enviado exitosamente!");
                $this->info("📧 Revisa tu bandeja de entrada en: {$email}");
                $this->info("📧 También revisa la carpeta de spam");
            } else {
                $this->error("❌ Error al enviar email con SendGrid API");
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("❌ Error al enviar email: " . $e->getMessage());
            $this->error("📋 Stack trace: " . $e->getTraceAsString());
            
            $this->info("\n💡 Soluciones posibles:");
            $this->info("1. Verifica que la API key de SendGrid sea válida");
            $this->info("2. Verifica que el email de origen esté verificado en SendGrid");
            $this->info("3. Revisa los logs de Laravel para más detalles");
            
            return 1;
        }

        return 0;
    }
} 