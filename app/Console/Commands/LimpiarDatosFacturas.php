<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarDatosFacturas extends Command
{
    protected $signature = 'facturas:limpiar-datos';
    protected $description = 'Limpia datos de fechas en facturas para evitar errores de formato';

    public function handle()
    {
        $this->info('Limpiando datos de fechas en facturas...');
        
        try {
            // Limpiar campos de fecha que puedan estar como NULL
            $updated = DB::table('facturas')
                ->whereNull('fecha_firma')
                ->update(['fecha_firma' => null]);
            $this->line("Campos fecha_firma limpiados: $updated");

            $updated = DB::table('facturas')
                ->whereNull('fecha_emision_email')
                ->update(['fecha_emision_email' => null]);
            $this->line("Campos fecha_emision_email limpiados: $updated");

            // Establecer estados por defecto si están vacíos o nulos
            $updated = DB::table('facturas')
                ->whereNull('estado_firma')
                ->orWhere('estado_firma', '')
                ->update(['estado_firma' => 'PENDIENTE']);
            $this->line("Estados de firma establecidos: $updated");

            $updated = DB::table('facturas')
                ->whereNull('estado_emision')
                ->orWhere('estado_emision', '')
                ->update(['estado_emision' => 'PENDIENTE']);
            $this->line("Estados de emisión establecidos: $updated");

            $this->info('Limpieza completada exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error durante la limpieza: ' . $e->getMessage());
            return 1;
        }
        return 0;
    }
}
