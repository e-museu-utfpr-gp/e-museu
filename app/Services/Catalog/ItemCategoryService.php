<?php

namespace App\Services\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Support\Content\TranslationDisplaySql;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Support\Admin\{AdminIndexConfig, AdminIndexQueryBuilder};

class ItemCategoryService
{
    /**
     * @return array{itemCategories: LengthAwarePaginator<int, ItemCategory>, count: int}
     */
    public function getPaginatedItemCategoriesForAdminIndex(Request $request): array
    {
        $nameSql = TranslationDisplaySql::itemCategoryNameSubquerySql('item_categories');
        $query = ItemCategory::query()
            ->select('item_categories.*')
            ->selectRaw("({$nameSql}) AS name")
            ->with('locks');

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::itemCategories());

        $itemCategories = $query->paginate(50)->withQueryString();

        return [
            'itemCategories' => $itemCategories,
            'count' => $itemCategories->total(),
        ];
    }

    /**
     * @return Collection<int, ItemCategory>
     */
    public function getForIndex(): Collection
    {
        $nameSql = TranslationDisplaySql::itemCategoryNameSubquerySql('item_categories');

        return ItemCategory::query()
            ->select('item_categories.id')
            ->selectRaw("({$nameSql}) AS name")
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, ItemCategory>
     */
    public function getForForm(): Collection
    {
        $nameSql = TranslationDisplaySql::itemCategoryNameSubquerySql('item_categories');

        return ItemCategory::query()
            ->select('item_categories.*')
            ->selectRaw("({$nameSql}) AS name")
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createItemCategory(array $data): ItemCategory
    {
        $translations = $data['translations'] ?? [];
        $category = ItemCategory::create(Arr::except($data, ['translations']));
        $category->syncTranslationsFromAdminForm($translations);

        return $category;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateItemCategory(ItemCategory $itemCategory, array $data): void
    {
        $translations = $data['translations'] ?? [];
        $persist = Arr::except($data, ['translations']);
        if ($persist !== []) {
            $itemCategory->update($persist);
        }
        if ($translations !== []) {
            $itemCategory->syncTranslationsFromAdminForm($translations);
        }
    }

    public function deleteItemCategory(ItemCategory $itemCategory): void
    {
        $itemCategory->delete();
    }
}
