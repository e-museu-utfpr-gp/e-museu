<?php

namespace Database\Seeders\Catalog;

use App\Enums\Content\ContentLanguage;
use App\Models\Catalog\ItemCategory;
use App\Models\Language;
use Illuminate\Database\Seeder;

/**
 * Demo categories: pt_BR (primary) + en + neutral (same label as pt for manual multi-locale checks).
 */
class ItemCategorySeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $enId = Language::query()->where('code', 'en')->value('id');
        $neutralId = Language::query()->where('code', ContentLanguage::NEUTRAL->value)->value('id');

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
            if ($neutralId !== null) {
                $category->translations()->updateOrCreate(
                    ['language_id' => $neutralId],
                    ['name' => $pt],
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
