<?php

namespace App\Services\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Content\TranslatablePayload;
use App\Support\Content\TranslationDisplaySql;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ItemCategoryService
{
    /**
     * @return array{itemCategories: LengthAwarePaginator<int, ItemCategory>, count: int}
     */
    public function getPaginatedItemCategoriesForAdminIndex(Request $request): array
    {
        $count = ItemCategory::count();
        $nameSql = TranslationDisplaySql::itemCategoryNameSubquerySql('item_categories');
        $query = ItemCategory::query()
            ->select('item_categories.*')
            ->selectRaw("({$nameSql}) AS name");

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::itemCategories());

        $itemCategories = $query->paginate(50)->withQueryString();

        return ['itemCategories' => $itemCategories, 'count' => $count];
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
        $split = TranslatablePayload::split($data, TranslatablePayload::ITEM_CATEGORY_KEYS);
        $category = ItemCategory::create($split['persist']);
        $category->syncPrimaryLocaleTranslation([
            'name' => (string) ($split['translation']['name'] ?? ''),
        ]);

        return $category;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateItemCategory(ItemCategory $itemCategory, array $data): void
    {
        $split = TranslatablePayload::split($data, TranslatablePayload::ITEM_CATEGORY_KEYS);
        if ($split['persist'] !== []) {
            $itemCategory->update($split['persist']);
        }
        if (array_key_exists('name', $split['translation'])) {
            $itemCategory->syncPrimaryLocaleTranslation([
                'name' => (string) $split['translation']['name'],
            ]);
        }
    }

    public function deleteItemCategory(ItemCategory $itemCategory): void
    {
        $itemCategory->delete();
    }
}
