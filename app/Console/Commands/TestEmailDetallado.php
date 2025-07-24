<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Models\Factura;
use Illuminate\Support\Facades\Log;

class TestEmailDetallado extends Command
{
    protected $signature = 'test:email-detallado {email}';
    protected $description = 'Probar envío de email con diagnóstico detallado';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🔍 Iniciando diagnóstico de email...");
        $this->info("📧 Email destino: {$email}");
        
        // 1. Verificar configuración
        $this->info("\n📋 Verificando configuración...");
        $emailService = new EmailService();
        $config = $emailService->verificarConfiguracion();
        
        foreach ($config as $key => $value) {
            $this->info("  {$key}: {$value}");
        }
        
        // 2. Verificar si hay facturas emitidas
        $this->info("\n📄 Buscando facturas emitidas...");
        $factura = Factura::where('estado', 'EMITIDA')->first();
        
        if (!$factura) {
            $this->error("❌ No hay facturas emitidas. Creando una de prueba...");
            
            // Crear factura de prueba
            $factura = new Factura();
            $factura->numero_factura = '001-001-000000001';
            $factura->cliente_id = 1;
            $factura->subtotal = 100.00;
            $factura->iva = 12.00;
            $factura->total = 112.00;
            $factura->estado = 'EMITIDA';
            $factura->usuario_id = 1;
            $factura->save();
            
            $this->info("✅ Factura de prueba creada con ID: {$factura->id}");
        } else {
            $this->info("✅ Factura encontrada: #{$factura->numero_factura}");
        }
        
        // 3. Verificar librerías
        $this->info("\n📚 Verificando librerías...");
        
        if (class_exists('\SendGrid\SendGrid')) {
            $this->info("✅ SendGrid librería disponible");
        } else {
            $this->error("❌ SendGrid librería no disponible");
        }
        
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $this->info("✅ DomPDF librería disponible");
        } else {
            $this->error("❌ DomPDF librería no disponible");
        }
        
        // 4. Verificar vistas
        $this->info("\n👁️ Verificando vistas...");
        
        if (view()->exists('emails.factura')) {
            $this->info("✅ Vista emails.factura disponible");
        } else {
            $this->error("❌ Vista emails.factura no encontrada");
        }
        
        if (view()->exists('facturas.pdf')) {
            $this->info("✅ Vista facturas.pdf disponible");
        } else {
            $this->error("❌ Vista facturas.pdf no encontrada");
        }
        
        // 5. Probar generación de PDF
        $this->info("\n📄 Probando generación de PDF...");
        try {
            $html = view('facturas.pdf', compact('factura'))->render();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdfContent = $pdf->output();
            $this->info("✅ PDF generado correctamente (" . strlen($pdfContent) . " bytes)");
        } catch (\Exception $e) {
            $this->error("❌ Error generando PDF: " . $e->getMessage());
        }
        
        // 6. Probar generación de HTML
        $this->info("\n📧 Probando generación de HTML...");
        try {
            $mensaje = "Esta es una prueba de envío de factura.";
            $html = view('emails.factura', compact('factura', 'mensaje'))->render();
            $this->info("✅ HTML generado correctamente (" . strlen($html) . " bytes)");
        } catch (\Exception $e) {
            $this->error("❌ Error generando HTML: " . $e->getMessage());
        }
        
        // 7. Probar envío real
        $this->info("\n🚀 Probando envío real...");
        try {
            $resultado = $emailService->enviarFactura(
                $factura,
                $email,
                "Prueba de Factura - " . now()->format('d/m/Y H:i:s'),
                "Esta es una prueba de envío de factura desde el sistema."
            );
            
            if ($resultado) {
                $this->info("✅ Email enviado exitosamente");
                $this->info("📧 Revisa tu bandeja de entrada en: {$email}");
                $this->info("📧 También revisa la carpeta de spam");
            } else {
                $this->error("❌ Error al enviar email");
            }
        } catch (\Exception $e) {
            $this->error("❌ Excepción al enviar email: " . $e->getMessage());
            $this->error("📋 Stack trace: " . $e->getTraceAsString());
        }
        
        // 8. Verificar logs
        $this->info("\n📋 Últimos logs de email:");
        // Llamada a Log::getRecentLogs() eliminada porque no existe ese método en Laravel
        // Si necesitas ver los logs, asegúrate de que la configuración de logging esté correcta
        // y que los logs estén disponibles en el archivo de log.
        // Por ejemplo, si estás usando Monolog, los logs se guardan en storage/logs/laravel.log
        // y puedes usar el comando `tail -f storage/logs/laravel.log` para verlos en tiempo real.
        // Si estás usando otro sistema de logging, la forma de acceder a los logs puede variar.
        // Para este comando, hemos eliminado la llamada a Log::getRecentLogs() para evitar un error.
        // Si necesitas ver los logs, asegúrate de que la configuración de logging esté correcta
        // y que los logs estén disponibles en el archivo de log.
        // Por ejemplo, si estás usando Monolog, los logs se guardan en storage/logs/laravel.log
        // y puedes usar el comando `tail -f storage/logs/laravel.log` para verlos en tiempo real.
        // Si estás usando otro sistema de logging, la forma de acceder a los logs puede variar.
        // Para este comando, hemos eliminado la llamada a Log::getRecentLogs() para evitar un error.
        
        $this->info("\n🎯 Diagnóstico completado");
        $this->info("💡 Si no recibes el email, verifica:");
        $this->info("  1. Tu carpeta de spam");
        $this->info("  2. Que el email esté correcto");
        $this->info("  3. Los logs de Laravel en storage/logs/laravel.log");
        
        return 0;
    }
} 