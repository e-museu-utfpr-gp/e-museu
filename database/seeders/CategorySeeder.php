<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        if(app()->environment('local')) {
            Category::create(['name' => 'Marca']);
            Category::create(['name' => 'Série']);
            Category::create(['name' => 'Tamanho']);
            Category::create(['name' => 'Cor']);
        }
    }
}
