<?php

namespace Database\Seeders\Taxonomy;

use App\Models\Taxonomy\TagCategory;
use Illuminate\Database\Seeder;

class TagCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local')) {
            TagCategory::create(['name' => 'Marca']);
            TagCategory::create(['name' => 'Série']);
            TagCategory::create(['name' => 'Tamanho']);
            TagCategory::create(['name' => 'Cor']);
        }
    }
}
