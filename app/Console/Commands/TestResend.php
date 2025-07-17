<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Resend\Laravel\Facades\Resend;

class TestResend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:resend {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar envío de email con Resend';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Probando Resend para: {$email}");

        try {
            $result = Resend::emails()->send([
                'from' => 'onboarding@resend.dev',
                'to' => $email,
                'subject' => 'Prueba Resend - SowarTech',
                'html' => '<h1>¡Hola desde Resend!</h1><p>Si recibes este email, Resend está funcionando correctamente.</p><p>Fecha: ' . now()->format('d/m/Y H:i:s') . '</p>'
            ]);

            $this->info("✅ Email enviado exitosamente!");
            $this->info("ID del email: " . $result->id);
            $this->info("Revisa tu bandeja de entrada en: {$email}");

        } catch (\Exception $e) {
            $this->error("❌ Error al enviar email: " . $e->getMessage());
            $this->error("Detalles: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
} 