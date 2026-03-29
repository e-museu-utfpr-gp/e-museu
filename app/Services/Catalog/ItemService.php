<?php

namespace App\Services\Catalog;

use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Language;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Admin\AdminIndexQueryBuilder;
use App\Support\Catalog\ItemIndexQueryBuilder;
use App\Support\Content\TranslatablePayload;
use App\Support\Content\TranslationDisplaySql;
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
        $itemCategory = ItemCategory::query()
            ->with('translations.language')
            ->find($id);

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
        $nameSql = TranslationDisplaySql::itemNameSubquerySql('items');

        return Item::query()
            ->where('category_id', 'LIKE', $itemCategoryId)
            ->where('validation', true)
            ->select('items.*')
            ->orderByRaw("({$nameSql}) asc")
            ->get();
    }

    /**
     * Items in an item category for admin dependent selects (includes non-validated).
     *
     * @return Collection<int, Item>
     */
    public function getItemsByItemCategoryForAdminSelect(string $itemCategoryId): Collection
    {
        if ($itemCategoryId === '') {
            return new Collection();
        }

        $nameSql = TranslationDisplaySql::itemNameSubquerySql('items');

        return Item::query()
            ->where('category_id', 'LIKE', $itemCategoryId)
            ->select('items.id')
            ->selectRaw("({$nameSql}) AS name")
            ->orderByRaw("({$nameSql}) asc")
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
        $nameSql = TranslationDisplaySql::itemNameSubquerySql('items');

        $qb = Item::query()
            ->where('category_id', 'LIKE', $categoryId)
            ->where('validation', true)
            ->selectRaw("({$nameSql}) AS name");

        if ($query !== '') {
            $qb->whereRaw("({$nameSql}) LIKE ?", ['%' . $query . '%']);
        }

        return $qb->limit(10)->get();
    }

    public function countValidatedByNameAndCategory(string $name, string $categoryId): int
    {
        $langId = Language::idForPreferredFormLocale();

        return Item::query()
            ->where('category_id', $categoryId)
            ->where('validation', true)
            ->whereHas('translations', function ($q) use ($name, $langId): void {
                $q->where('language_id', $langId)
                    ->where('name', 'LIKE', $name);
            })
            ->count();
    }

    public function createItemWithIdentificationCode(AdminStoreItemRequest $request): Item
    {
        $itemAttributes = [
            'date' => $request->input('date'),
            'category_id' => $request->input('category_id'),
            'collaborator_id' => $request->input('collaborator_id'),
            'validation' => $request->boolean('validation'),
            'identification_code' => '000',
        ];

        $translationFields = [
            'name' => (string) $request->input('name'),
            'description' => (string) $request->input('description'),
            'history' => $request->input('history'),
            'detail' => $request->input('detail'),
        ];

        $item = null;

        DB::transaction(function () use ($itemAttributes, $translationFields, &$item): void {
            $item = Item::create($itemAttributes);
            $item->syncPrimaryLocaleTranslation($translationFields);
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
        $itemCategory = ItemCategory::query()
            ->with('translations.language')
            ->findOrFail($item->category_id);
        $normalized = StringHelper::removeAccent($itemCategory->defaultLocaleName());
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
        $split = TranslatablePayload::split($attributes, TranslatablePayload::ITEM_KEYS);
        $translationData = $split['translation'];
        $itemData = $split['persist'];

        if ($itemData !== []) {
            $item->update($itemData);
        }

        if ($translationData !== []) {
            $item->syncPrimaryLocaleTranslation($translationData);
        }

        $item->normalizeSingleCover();
    }

    public function deleteItem(Item $item): void
    {
        $item->delete();
    }
}
