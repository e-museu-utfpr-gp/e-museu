<?php

namespace App\Console\Commands\Identity;

use App\Models\Identity\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'create:admin';

    protected $description = 'Criar administrador';

    public function handle(): int
    {
        $username = $this->ask('Insira o nome de usuário do administrador:');
        $password = $this->secret('Insira a senha do administrador:');

        $admin = new Admin();
        $admin->username = $username;
        $admin->password = Hash::make($password);
        $admin->save();

        $this->info('Administrador adicionado com sucesso!');

        return Command::SUCCESS;
    }
}
