<?php

namespace Database\Seeders\Identity;

use App\Models\Identity\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        if (User::where('username', 'admin')->exists()) {
            return;
        }

        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);
    }
}
