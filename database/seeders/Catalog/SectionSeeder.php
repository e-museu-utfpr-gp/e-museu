<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        Section::create(['name' => 'Monitor']);
        Section::create(['name' => 'Notebook']);
        Section::create(['name' => 'Computador de Mesa']);
        Section::create(['name' => 'Fone']);
        Section::create(['name' => 'Mouse']);
        Section::create(['name' => 'Teclado']);
        Section::create(['name' => 'Impressora']);
        Section::create(['name' => 'Armazenamento']);
        Section::create(['name' => 'Placa de Vídeo']);
        Section::create(['name' => 'Webcam']);
        Section::create(['name' => 'Memória Ram']);
        Section::create(['name' => 'Roteador']);
        Section::create(['name' => 'Tablet']);
        Section::create(['name' => 'Celular']);
        Section::create(['name' => 'Placa-mãe']);
        Section::create(['name' => 'Processador']);
    }
}
