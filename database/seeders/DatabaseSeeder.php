<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProprietarySeeder::class,
            CategorySeeder::class,
            SectionSeeder::class,
            ItemSeeder::class,
            ExtraSeeder::class,
            TagSeeder::class,
        ]);
    }
}
