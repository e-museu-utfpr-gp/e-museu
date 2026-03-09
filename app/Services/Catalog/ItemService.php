<?php

namespace App\Services\Catalog;

use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use App\Support\AdminIndexQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ItemService
{
    /** @var array{baseTable: string, searchSpecial: array<string, array{table: string, column: string}>, sortSpecial: array<string, string>} */
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
     * Get sections (item categories) and collaborators for create/edit forms.
     *
     * @return array{sections: \Illuminate\Database\Eloquent\Collection<int, ItemCategory>,
     *               collaborators: \Illuminate\Database\Eloquent\Collection<int, Collaborator>}
     */
    public function getSectionsAndCollaborators(): array
    {
        return [
            'sections' => ItemCategory::orderBy('name')->get(),
            'collaborators' => Collaborator::orderBy('full_name')->get(),
        ];
    }

    /**
     * Create an item from the admin store request and set its identification code in a single transaction.
     */
    public function createItemWithIdentificationCode(
        AdminStoreItemRequest $request,
        ItemContributionService $itemContributionService
    ): Item {
        $data = [
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

        DB::transaction(function () use ($data, &$item, $itemContributionService): void {
            $item = Item::create($data);

            $updateData = array_merge($data, [
                'identification_code' => $itemContributionService->createIdentificationCode($item),
            ]);

            $item->update($updateData);
        });

        if (! $item instanceof Item) {
            throw new RuntimeException('Item creation failed');
        }

        return $item;
    }

    /**
     * Update an item with the given data and normalize its cover image.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateItem(Item $item, array $data): void
    {
        $item->update($data);
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
}
