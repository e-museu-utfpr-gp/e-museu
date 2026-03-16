<?php

namespace Database\Seeders\Identity;

use App\Models\Identity\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        if (Admin::where('username', 'admin')->exists()) {
            return;
        }

        Admin::create([
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);
    }
}
