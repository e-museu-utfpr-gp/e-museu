<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        ItemCategory::create(['name' => 'Monitor']);
        ItemCategory::create(['name' => 'Notebook']);
        ItemCategory::create(['name' => 'Computador de Mesa']);
        ItemCategory::create(['name' => 'Fone']);
        ItemCategory::create(['name' => 'Mouse']);
        ItemCategory::create(['name' => 'Teclado']);
        ItemCategory::create(['name' => 'Impressora']);
        ItemCategory::create(['name' => 'Armazenamento']);
        ItemCategory::create(['name' => 'Placa de Vídeo']);
        ItemCategory::create(['name' => 'Webcam']);
        ItemCategory::create(['name' => 'Memória Ram']);
        ItemCategory::create(['name' => 'Roteador']);
        ItemCategory::create(['name' => 'Tablet']);
        ItemCategory::create(['name' => 'Celular']);
        ItemCategory::create(['name' => 'Placa-mãe']);
        ItemCategory::create(['name' => 'Processador']);
    }
}
