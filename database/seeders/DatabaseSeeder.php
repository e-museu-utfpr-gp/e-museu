<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            \Database\Seeders\Collaborator\CollaboratorSeeder::class,
            \Database\Seeders\Identity\AdminSeeder::class,
            \Database\Seeders\Taxonomy\TagCategorySeeder::class,
            \Database\Seeders\Catalog\ItemCategorySeeder::class,
            \Database\Seeders\Catalog\ItemSeeder::class,
            \Database\Seeders\Catalog\ExtraSeeder::class,
            \Database\Seeders\Taxonomy\TagSeeder::class,
        ]);
    }
}
