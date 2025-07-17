<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;

class CheckClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:clientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar clientes existentes y sus emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientes = Cliente::all();
        
        $this->info('Clientes en BD: ' . $clientes->count());
        $this->info('Emails únicos: ' . $clientes->unique('email')->count());
        
        $this->info('Todos los clientes:');
        foreach ($clientes as $cliente) {
            $this->line('- ID: ' . $cliente->id . ' | Nombre: ' . $cliente->nombre . ' | Email: ' . ($cliente->email ?: 'SIN EMAIL'));
        }
        
        $this->info('Emails únicos:');
        $emailsUnicos = $clientes->pluck('email')->filter()->unique();
        foreach ($emailsUnicos as $email) {
            $this->line('- ' . $email);
        }
        
        return 0;
    }
}
