<?php

namespace App\Console\Commands\Identity;

use Illuminate\Console\Command;
use App\Models\Identity\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'create:admin';

    protected $description = 'Criar administrador';

    public function handle(): int
    {
        $username = $this->ask('Insira o nome de usuário do administrador:');
        $password = $this->secret('Insira a senha do administrador:');

        $user = new User();
        $user->username = $username;
        $user->password = Hash::make($password);
        $user->is_admin = true;
        $user->save();

        $this->info('Administrador adicionado com sucesso!');

        return Command::SUCCESS;
    }
}
