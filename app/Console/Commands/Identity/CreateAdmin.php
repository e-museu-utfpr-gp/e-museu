<?php

declare(strict_types=1);

namespace App\Console\Commands\Identity;

use App\Models\Identity\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'create:admin';

    protected $description = 'Create an administrator account';

    public function handle(): int
    {
        $username = $this->ask('Administrator username:');
        $password = $this->secret('Administrator password:');

        $admin = new Admin();
        $admin->username = $username;
        $admin->password = Hash::make($password);
        $admin->save();

        $this->info('Administrator created successfully.');

        return Command::SUCCESS;
    }
}
