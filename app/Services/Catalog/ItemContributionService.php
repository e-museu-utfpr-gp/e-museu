<?php

namespace App\Services\Catalog;

use App\Models\Catalog\Extra;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemComponent;
use App\Models\Catalog\ItemCategory;
use App\Enums\CollaboratorRole;
use App\Models\Collaborator\Collaborator;
use App\Models\Taxonomy\TagCategory;
use App\Models\Taxonomy\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ItemContributionService
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, TagCategory>
     */
    public function loadCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return TagCategory::select('name', 'id')->orderBy('name', 'asc')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, ItemCategory>
     */
    public function loadSections(): \Illuminate\Database\Eloquent\Collection
    {
        return ItemCategory::select('name', 'id')->orderBy('name', 'asc')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Tag>
     */
    public function loadTags(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Tag::select('name', 'id')
            ->where('validation', true)
            ->where('tag_category_id', $category)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $collaboratorData
     * @param  array<string, mixed>  $itemData
     * @param  array<int, array<string, mixed>>  $tags
     * @param  array<int, array<string, mixed>>  $extras
     * @param  array<int, array<string, mixed>>  $components
     */
    public function store(
        array $collaboratorData,
        array $itemData,
        array $tags,
        array $extras,
        array $components,
        ?UploadedFile $image
    ): RedirectResponse {
        $collaborator = Collaborator::where('contact', '=', $collaboratorData['contact'])->first();
        if (! $collaborator) {
            $collaborator = $this->storeCollaborator($collaboratorData);
        }

        if ($collaborator->role === CollaboratorRole::INTERNAL) {
            return back()->withErrors(['contact' => __('app.collaborator.contact_reserved_for_internal')]);
        }

        if ($collaborator->blocked === true) {
            return back()->withErrors(['blocked' => __('app.collaborator.blocked_from_registering')]);
        }

        if ($image) {
            unset($itemData['image']);
        }

        $item = $this->storeItem($itemData, $collaborator);

        if ($image) {
            $ext = $image->getClientOriginalExtension() ?: 'png';
            $path = Item::buildImagePath($item, $ext);
            $contents = $image->get();
            if ($contents === false) {
                throw new RuntimeException(__('app.catalog.item.upload_read_failed'));
            }
            Storage::disk('public')->put($path, $contents);
            $item->update(['image' => $path]);
        }

        $this->storeMultipleTag($tags, $item);
        $this->storeMultipleExtra($extras, $item, $collaborator);
        $this->storeMultipleComponent($components, $item);

        return redirect()->route('items.create')->with('success', __('app.catalog.item.contribution_success'));
    }

    /**
     * @param  array<string, mixed>  $collaboratorData
     * @param  array<string, mixed>  $extraData
     */
    public function storeSingleExtra(array $collaboratorData, array $extraData): RedirectResponse
    {
        $collaborator = Collaborator::where('contact', $collaboratorData['contact'])->first();
        if (! $collaborator) {
            $collaborator = $this->storeCollaborator($collaboratorData);
        }

        if ($collaborator->role === CollaboratorRole::INTERNAL) {
            return back()->withErrors(['contact' => __('app.collaborator.contact_reserved_for_internal')]);
        }

        if ($collaborator->blocked === true) {
            return back()->withErrors(['blocked' => __('app.collaborator.blocked_from_registering')]);
        }

        $extraData['collaborator_id'] = $collaborator->id;
        Extra::create($extraData);

        return back()->with('success', __('app.catalog.extra.contribution_success'));
    }

    /**
     * @param  array<string, mixed>  $collaboratorData
     */
    private function storeCollaborator(array $collaboratorData): Collaborator
    {
        $collaboratorData['role'] = CollaboratorRole::EXTERNAL;

        return Collaborator::create($collaboratorData);
    }

    /**
     * @param  array<string, mixed>  $itemData
     */
    private function storeItem(array $itemData, Collaborator $collaborator): Item
    {
        $itemData['collaborator_id'] = $collaborator->id;
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
     * @param  array<int, array<string, mixed>>  $tags
     */
    private function storeMultipleTag(array $tags, Item $item): void
    {
        foreach ($tags as $tagData) {
            $tagCategoryId = $tagData['tag_category_id'] ?? $tagData['category_id'] ?? null;
            $tag = Tag::where('tag_category_id', '=', $tagCategoryId)
                ->where('name', '=', $tagData['name'])
                ->first();
            if ($tag === null) {
                $createData = $tagData;
                $createData['tag_category_id'] = $tagCategoryId;
                unset($createData['category_id']);
                $tag = Tag::create($createData);
            }
            $item->tags()->attach($tag->id);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $extras
     */
    private function storeMultipleExtra(array $extras, Item $item, Collaborator $collaborator): void
    {
        foreach ($extras as $extraItemData) {
            $extraItemData['collaborator_id'] = $collaborator->id;
            $extraItemData['item_id'] = $item->id;
            Extra::create($extraItemData);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $components
     */
    private function storeMultipleComponent(array $components, Item $item): void
    {
        foreach ($components as $componentItemData) {
            $component = Item::where('category_id', '=', $componentItemData['category_id'])
                ->where('name', '=', $componentItemData['name'])
                ->first();
            if (! $component) {
                continue;
            }
            $componentItemData['component_id'] = $component->id;
            $componentItemData['item_id'] = $item->id;
            ItemComponent::create($componentItemData);
        }
    }

    private function createIdentificationCode(Item $item): string
    {
        $categoryModel = ItemCategory::findOrFail($item->category_id);
        $sectionNameNormalized = $this->removeAccent($categoryModel->name);
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
