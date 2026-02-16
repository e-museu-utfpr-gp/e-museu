<?php

namespace App\Services\Catalog;

use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemComponent;
use App\Models\Catalog\Section;
use App\Models\Proprietary\Proprietary;
use App\Models\Taxonomy\Category;
use App\Models\Taxonomy\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class ItemContributionService
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Category>
     */
    public function loadCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::select('name', 'id')->orderBy('name', 'asc')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Section>
     */
    public function loadSections(): \Illuminate\Database\Eloquent\Collection
    {
        return Section::select('name', 'id')->orderBy('name', 'asc')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Tag>
     */
    public function loadTags(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Tag::select('name', 'id')
            ->where('validation', true)
            ->where('category_id', $category)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param array<string, mixed> $proprietaryData
     * @param array<string, mixed> $itemData
     * @param array<int, array<string, mixed>> $tags
     * @param array<int, array<string, mixed>> $extras
     * @param array<int, array<string, mixed>> $components
     */
    public function store(
        array $proprietaryData,
        array $itemData,
        array $tags,
        array $extras,
        array $components,
        ?UploadedFile $image
    ): RedirectResponse {
        $proprietary = Proprietary::where('contact', '=', $proprietaryData['contact'])->first();
        if (!$proprietary) {
            $proprietary = $this->storeProprietary($proprietaryData);
        }

        if ($proprietary->blocked === true) {
            return back()->withErrors(['Este usuário não possui permissão para registrar itens.']);
        }

        if ($image) {
            $itemData['image'] = $image->store('items');
        }

        $item = $this->storeItem($itemData, $proprietary);
        $this->storeMultipleTag($tags, $item);
        $this->storeMultipleExtra($extras, $item, $proprietary);
        $this->storeMultipleComponent($components, $item);

        $successMessage = 'Agradecemos pelo seu tempo! Analisaremos sua colaboração '
            . 'antes de adicionarmos ao nosso museu.';

        return redirect()->route('items.create')->with('success', $successMessage);
    }

    /**
     * @param array<string, mixed> $proprietaryData
     * @param array<string, mixed> $extraData
     */
    public function storeSingleExtra(array $proprietaryData, array $extraData): RedirectResponse
    {
        $proprietary = Proprietary::where('contact', $proprietaryData['contact'])->first();
        if (!$proprietary) {
            $proprietary = $this->storeProprietary($proprietaryData);
        }

        if ($proprietary->blocked === true) {
            return back()->withErrors(['Este usuário não possui permissão para registrar itens.']);
        }

        $extraData['proprietary_id'] = $proprietary->id;
        Extra::create($extraData);

        $successMessage = 'Curiosidade extra enviada com sucesso! Agradecemos pelo seu tempo, '
            . 'analisaremos sua proposta antes de adicionarmos ao nosso museu.';

        return back()->with('success', $successMessage);
    }

    /**
     * @param array<string, mixed> $proprietaryData
     */
    private function storeProprietary(array $proprietaryData): Proprietary
    {
        return Proprietary::create($proprietaryData);
    }

    /**
     * @param array<string, mixed> $itemData
     */
    private function storeItem(array $itemData, Proprietary $proprietary): Item
    {
        $itemData['proprietary_id'] = $proprietary->id;
        $itemData['identification_code'] = '000';
        if (array_key_exists('date', $itemData) && $itemData['date'] === null) {
            $itemData['date'] = '0001-01-01 00:00:00';
        }

        return DB::transaction(function () use ($itemData): Item {
            $item = Item::create($itemData);
            $itemData['identification_code'] = $this->createIdentificationCode($item);
            $item->update($itemData);

            return $item;
        });
    }

    /**
     * @param array<int, array<string, mixed>> $tags
     */
    private function storeMultipleTag(array $tags, Item $item): void
    {
        foreach ($tags as $tagData) {
            $tag = Tag::where('category_id', '=', $tagData['category_id'])
                ->where('name', '=', $tagData['name'])
                ->first();
            if ($tag === null) {
                $tag = Tag::create($tagData);
            }
            $item->tags()->attach($tag->id);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $extras
     */
    private function storeMultipleExtra(array $extras, Item $item, Proprietary $proprietary): void
    {
        foreach ($extras as $extraItemData) {
            $extraItemData['proprietary_id'] = $proprietary->id;
            $extraItemData['item_id'] = $item->id;
            Extra::create($extraItemData);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $components
     */
    private function storeMultipleComponent(array $components, Item $item): void
    {
        foreach ($components as $componentItemData) {
            $component = Item::where('section_id', '=', $componentItemData['category_id'])
                ->where('name', '=', $componentItemData['name'])
                ->first();
            if (!$component) {
                continue;
            }
            $componentItemData['component_id'] = $component->id;
            $componentItemData['item_id'] = $item->id;
            ItemComponent::create($componentItemData);
        }
    }

    private function createIdentificationCode(Item $item): string
    {
        $sectionModel = Section::findOrFail($item->section_id);
        $sectionNameNormalized = $this->removeAccent($sectionModel->name);
        $words = explode(' ', $sectionNameNormalized);
        if (count($words) === 1) {
            $words = explode('-', $words[0]);
        }
        if (count($words) > 1) {
            $sectionCode = strtoupper(substr($words[0], 0, 2)) . strtoupper(substr(end($words), 0, 2));
        } else {
            $sectionCode = strtoupper(substr($words[0], 0, 4));
        }

        return 'EXT_' . $sectionCode . '_' . $item->id;
    }

    private function removeAccent(string $string): string
    {
        $withoutAccents = preg_replace(
            [
                '/(á|à|ã|â|ä)/', '/(Á|À|Ã|Â|Ä)/',
                '/(é|è|ê|ë)/', '/(É|È|Ê|Ë)/',
                '/(í|ì|î|ï)/', '/(Í|Ì|Î|Ï)/',
                '/(ó|ò|õ|ô|ö)/', '/(Ó|Ò|Õ|Ô|Ö)/',
                '/(ú|ù|û|ü)/', '/(Ú|Ù|Û|Ü)/',
                '/(ñ)/', '/(Ñ)/',
            ],
            explode(' ', 'a A e E i I o O u U n N'),
            $string
        );

        return $withoutAccents ?? $string;
    }
}
