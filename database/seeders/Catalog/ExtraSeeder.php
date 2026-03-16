<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Extra;
use Illuminate\Database\Seeder;

class ExtraSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            Extra::factory(100)->create();
        }
    }
}
