<?php

namespace Database\Seeders\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Models\Language;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $enId = Language::query()->where('code', 'en')->value('id');

        foreach ($this->labels() as [$pt, $en]) {
            $category = new ItemCategory();
            $category->save();
            $category->syncPrimaryLocaleTranslation(['name' => $pt]);
            if ($enId !== null) {
                $category->translations()->updateOrCreate(
                    ['language_id' => $enId],
                    ['name' => $en],
                );
            }
        }
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    private function labels(): array
    {
        return [
            ['Monitor', 'Monitor'],
            ['Notebook', 'Notebook'],
            ['Computador de Mesa', 'Desktop computer'],
            ['Fone', 'Headphones'],
            ['Mouse', 'Mouse'],
            ['Teclado', 'Keyboard'],
            ['Impressora', 'Printer'],
            ['Armazenamento', 'Storage'],
            ['Placa de Vídeo', 'Graphics card'],
            ['Webcam', 'Webcam'],
            ['Memória Ram', 'RAM'],
            ['Roteador', 'Router'],
            ['Tablet', 'Tablet'],
            ['Celular', 'Mobile phone'],
            ['Placa-mãe', 'Motherboard'],
            ['Processador', 'Processor'],
        ];
    }
}
