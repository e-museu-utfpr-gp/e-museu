<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
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
