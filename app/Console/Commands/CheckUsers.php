<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar emails existentes en la tabla users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $this->info('Usuarios en BD: ' . $users->count());
        $this->info('Emails existentes:');
        foreach ($users as $user) {
            $this->line('- ' . $user->email . ' (ID: ' . $user->id . ')');
        }
        return 0;
    }
}
