<?php

namespace App\Services\Catalog;

use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Support\AdminIndexQueryBuilder;
use App\Support\AdminIndexConfig;
use App\Support\ItemIndexQueryBuilder;
use App\Support\StringHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ItemService
{
    /**
     * @return array{items: LengthAwarePaginator<int, Item>, count: int}
     */
    public function getPaginatedItemsForAdminIndex(Request $request): array
    {
        $count = Item::count();
        $query = Item::query()->forAdminList();

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::items());

        $items = $query->paginate(30)->withQueryString();

        return ['items' => $items, 'count' => $count];
    }

    /**
     * @return array{items: LengthAwarePaginator<int, Item>, categoryName: string}
     */
    public function getPaginatedItemsForCatalogIndex(Request $request): array
    {
        $query = ItemIndexQueryBuilder::build($request);
        $itemCategoryId = $request->item_category ?? $request->input('item_category');
        $order = $request->input('order', 1);
        $items = $query->paginate(24)->withQueryString()->appends([
            'item_category' => $itemCategoryId,
            'order' => $order,
        ]);
        $categoryName = $this->getItemCategoryName($itemCategoryId);

        return ['items' => $items, 'categoryName' => $categoryName];
    }

    /**
     * Resolve item category name by id (for catalog index).
     */
    public function getItemCategoryName(?string $id): string
    {
        if (! $id) {
            return '';
        }
        $itemCategory = ItemCategory::find($id);

        return $itemCategory !== null ? $itemCategory->name : '';
    }

    public function getPublicItemForShow(string $id): Item
    {
        $item = Item::query()->withCatalogShowRelations()->findOrFail($id);

        if (! $item->validation) {
            abort(403, __('app.catalog.item.access_denied'));
        }

        return $item;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getPublicItemsByCategory(string $itemCategoryId): Collection
    {
        return Item::where('category_id', 'LIKE', $itemCategoryId)
            ->where('validation', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Random validated items for the public home page carousel.
     *
     * @return Collection<int, Item>
     */
    public function getRandomValidatedItemsForHome(int $limit = 5): Collection
    {
        return Item::with('coverImage')
            ->where('validation', true)
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    /**
     * @return Collection<int, Item>
     */
    public function getValidatedNamesForComponentAutocomplete(string $query, string $categoryId): Collection
    {
        $qb = Item::select('name')
            ->where('category_id', 'LIKE', $categoryId)
            ->where('validation', true);

        if ($query !== '') {
            $qb = $qb->where('name', 'LIKE', '%' . $query . '%');
        }

        return $qb->limit(10)->get();
    }

    public function countValidatedByNameAndCategory(string $name, string $categoryId): int
    {
        return Item::where('category_id', $categoryId)
            ->where('name', 'LIKE', $name)
            ->where('validation', true)
            ->count();
    }

    public function createItemWithIdentificationCode(AdminStoreItemRequest $request): Item
    {
        $itemAttributes = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'history' => $request->input('history'),
            'detail' => $request->input('detail'),
            'date' => $request->input('date'),
            'category_id' => $request->input('category_id'),
            'collaborator_id' => $request->input('collaborator_id'),
            'validation' => $request->boolean('validation'),
            'identification_code' => '000',
        ];

        $item = null;

        DB::transaction(function () use ($itemAttributes, &$item): void {
            $item = Item::create($itemAttributes);
            $item->update([
                'identification_code' => $this->createIdentificationCode($item),
            ]);
        });

        if (! $item instanceof Item) {
            throw new RuntimeException('Item creation failed');
        }

        return $item;
    }

    public function createIdentificationCode(Item $item): string
    {
        $itemCategory = ItemCategory::findOrFail($item->category_id);
        $normalized = StringHelper::removeAccent($itemCategory->name);
        $nameParts = explode(' ', $normalized);
        if (count($nameParts) === 1) {
            $nameParts = explode('-', $nameParts[0]);
        }
        if (count($nameParts) > 1) {
            $code = strtoupper(substr($nameParts[0], 0, 2)) . strtoupper(substr(end($nameParts), 0, 2));
        } else {
            $code = strtoupper(substr($nameParts[0], 0, 4));
        }

        return 'EXT_' . $code . '_' . $item->id;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateItem(Item $item, array $attributes): void
    {
        $item->update($attributes);
        $item->normalizeSingleCover();
    }

    public function deleteItem(Item $item): void
    {
        $item->delete();
    }
}
