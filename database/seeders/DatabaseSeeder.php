<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            \Database\Seeders\Proprietary\ProprietarySeeder::class,
            \Database\Seeders\Identity\UserSeeder::class,
            \Database\Seeders\Taxonomy\CategorySeeder::class,
            \Database\Seeders\Catalog\SectionSeeder::class,
            \Database\Seeders\Catalog\ItemSeeder::class,
            \Database\Seeders\Catalog\ExtraSeeder::class,
            \Database\Seeders\Taxonomy\TagSeeder::class,
        ]);
    }
}
