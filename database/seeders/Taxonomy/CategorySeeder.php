<?php

namespace Database\Seeders\Taxonomy;

use App\Models\Taxonomy\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            Category::create(['name' => 'Marca']);
            Category::create(['name' => 'Série']);
            Category::create(['name' => 'Tamanho']);
            Category::create(['name' => 'Cor']);
        }
    }
}
