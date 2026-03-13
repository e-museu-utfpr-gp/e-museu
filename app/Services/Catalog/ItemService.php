<?php

namespace App\Services\Catalog;

use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Support\AdminIndexQuery;
use App\Support\StringHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ItemService
{
    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>, booleanColumns: array<int, string>} */
    private const ADMIN_INDEX_CONFIG = [
        'baseTable' => 'items',
        'searchSpecial' => [
            'collaborator_id' => ['table' => 'collaborators', 'column' => 'contact'],
            'category_id' => ['table' => 'item_categories', 'column' => 'name'],
        ],
        'sortSpecial' => [
            'collaborator_id' => 'collaborators.contact',
            'category_id' => 'item_categories.name',
        ],
        'booleanColumns' => ['validation'],
    ];

    /**
     * Get paginated items and total count for the admin items index (with search and sort applied).
     *
     * @return array{items: LengthAwarePaginator<int, Item>, count: int}
     */
    public function getPaginatedItemsForAdminIndex(Request $request): array
    {
        $count = Item::count();
        $query = Item::query()->forAdminList();

        AdminIndexQuery::applySearch($query, $request->search_column, $request->search, self::ADMIN_INDEX_CONFIG);
        AdminIndexQuery::applySort($query, $request->sort, $request->order, self::ADMIN_INDEX_CONFIG);

        $items = $query->paginate(30)->withQueryString();

        return ['items' => $items, 'count' => $count];
    }

    /**
     * Create an item from the admin store request and set its identification code in a single transaction.
     */
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

            $attributesWithIdentificationCode = array_merge($itemAttributes, [
                'identification_code' => $this->createIdentificationCode($item),
            ]);

            $item->update($attributesWithIdentificationCode);
        });

        if (! $item instanceof Item) {
            throw new RuntimeException('Item creation failed');
        }

        return $item;
    }

    /**
     * Generate identification code for an item (e.g. EXT_ABCD_123).
     */
    public function createIdentificationCode(Item $item): string
    {
        $itemCategory = ItemCategory::findOrFail($item->category_id);
        $normalizedItemCategoryName = StringHelper::removeAccent($itemCategory->name);
        $nameParts = explode(' ', $normalizedItemCategoryName);
        if (count($nameParts) === 1) {
            $nameParts = explode('-', $nameParts[0]);
        }
        if (count($nameParts) > 1) {
            $itemCategoryCode = strtoupper(substr($nameParts[0], 0, 2)) . strtoupper(substr(end($nameParts), 0, 2));
        } else {
            $itemCategoryCode = strtoupper(substr($nameParts[0], 0, 4));
        }

        return 'EXT_' . $itemCategoryCode . '_' . $item->id;
    }

    /**
     * Update an item with the given attributes and normalize its cover image.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function updateItem(Item $item, array $attributes): void
    {
        $item->update($attributes);
        $item->normalizeSingleCover();
    }

    /**
     * Delete an item from the database.
     * Call ItemImagesService::deleteAllImagesForItem first if the item has images to remove.
     */
    public function deleteItem(Item $item): void
    {
        $item->delete();
    }

    /**
     * Get a single validated item for public show page (with images eager loaded).
     */
    public function getPublicItemForShow(string $id): Item
    {
        $item = Item::with('images')->findOrFail($id);

        if (! $item->validation) {
            abort(403, __('app.catalog.item.access_denied'));
        }

        return $item;
    }

    /**
     * Get validated items for public listing filtered by item category.
     *
     * @return Collection<int, Item>
     */
    public function getPublicItemsByCategory(string $itemCategoryId): Collection
    {
        return Item::where('category_id', 'LIKE', $itemCategoryId)
            ->where('validation', true)
            ->orderBy('name', 'asc')
            ->get();
    }
}
