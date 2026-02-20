<?php

namespace Database\Seeders\Proprietary;

use App\Models\Proprietary\Proprietary;
use Illuminate\Database\Seeder;

class ProprietarySeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            Proprietary::create([
                'full_name' => 'UNICENTRO',
                'contact' => 'unicentro@unicentro.com',
                'blocked' => '0',
                'is_admin' => '1',
            ]);

            Proprietary::create([
                'full_name' => 'UTFPR',
                'contact' => 'utfpr@utfpr.com',
                'blocked' => '0',
                'is_admin' => '1',
            ]);

            Proprietary::factory(30)->create();
        }
    }
}
