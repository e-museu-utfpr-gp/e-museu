<?php

namespace App\Services\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Support\Admin\AdminIndexConfig;
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
        $query = ItemCategory::query();

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::itemCategories());

        $itemCategories = $query->paginate(50)->withQueryString();

        return ['itemCategories' => $itemCategories, 'count' => $count];
    }

    /**
     * @return Collection<int, ItemCategory>
     */
    public function getForIndex(): Collection
    {
        return ItemCategory::select('name', 'id')->orderBy('name', 'asc')->get();
    }

    /**
     * @return Collection<int, ItemCategory>
     */
    public function getForForm(): Collection
    {
        return ItemCategory::orderBy('name')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createItemCategory(array $data): ItemCategory
    {
        return ItemCategory::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateItemCategory(ItemCategory $itemCategory, array $data): void
    {
        $itemCategory->update($data);
    }

    public function deleteItemCategory(ItemCategory $itemCategory): void
    {
        $itemCategory->delete();
    }
}
