<?php

namespace App\Services\Catalog;

use App\Http\Requests\Admin\Catalog\AdminStoreItemRequest;
use App\Models\Language;
use App\Models\Catalog\{Item, ItemCategory};
use App\Support\Catalog\ItemIdentificationCode;
use App\Support\Admin\{AdminIndexConfig, AdminIndexQueryBuilder};
use App\Support\Catalog\ItemIndexQueryBuilder;
use App\Support\Content\{TranslatablePayload, TranslationDisplaySql};
use App\Support\Database\SqlExpr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Catalog {@see Item} persistence, listings, and item-scoped public queries (not contribution-locale UI).
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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
        $appends = [
            'item_category' => $itemCategoryId,
            'order' => $order,
        ];
        if ($request->filled('location_id')) {
            $appends['location_id'] = $request->input('location_id');
        }
        $items = $query->paginate(24)->withQueryString()->appends($appends);
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
        $categoryId = $this->normalizeItemCategoryFilterId($itemCategoryId);
        if ($categoryId === null) {
            return new Collection();
        }

        $nameSql = TranslationDisplaySql::itemNameSubquerySql('items');

        $query = Item::query()
            ->where('category_id', '=', $categoryId)
            ->where('validation', true)
            ->with('location')
            ->select('items.*');
        SqlExpr::orderByRaw($query, "({$nameSql}) asc");

        return $query->get();
    }

    /**
     * Validated items in a category: `name` prefers the contribution language when present,
     * otherwise the same fallback chain as the rest of the site ({@see TranslationDisplaySql::itemNameSubquerySql}).
     *
     * @return Collection<int, Item>
     */
    public function getPublicItemsByCategoryForLanguage(string $itemCategoryId, int $languageId): Collection
    {
        $categoryId = $this->normalizeItemCategoryFilterId($itemCategoryId);
        if ($categoryId === null) {
            return new Collection();
        }

        $fallbackNameSql = TranslationDisplaySql::itemNameSubquerySql('items');
        $resolvedNameSql = 'COALESCE(it_contribution.name, (' . $fallbackNameSql . '))';

        $query = Item::query()
            ->where('category_id', '=', $categoryId)
            ->where('validation', true)
            ->leftJoin('item_translations as it_contribution', function ($join) use ($languageId): void {
                $join->on('items.id', '=', 'it_contribution.item_id')
                    ->where('it_contribution.language_id', '=', $languageId);
            })
            ->select('items.id', 'items.location_id')
            ->with('location');
        SqlExpr::selectRaw($query, "{$resolvedNameSql} AS name");
        SqlExpr::orderByRaw($query, "{$resolvedNameSql} asc");

        return $query->get();
    }

    /**
     * Items in an item category for admin dependent selects (includes non-validated).
     *
     * @return Collection<int, Item>
     */
    public function getItemsByItemCategoryForAdminSelect(string $itemCategoryId): Collection
    {
        $categoryId = $this->normalizeItemCategoryFilterId($itemCategoryId);
        if ($categoryId === null) {
            return new Collection();
        }

        $nameSql = TranslationDisplaySql::itemNameSubquerySql('items');

        $query = Item::query()
            ->where('category_id', '=', $categoryId)
            ->with('location')
            ->select('items.id', 'items.location_id');
        SqlExpr::selectRaw($query, "({$nameSql}) AS name");
        SqlExpr::orderByRaw($query, "({$nameSql}) asc");

        return $query->get();
    }

    /**
     * JSON payload for {@see \App\Http\Controllers\Catalog\ItemController::byCategory} and
     * {@see \App\Http\Controllers\Admin\Catalog\AdminItemController::byItemCategory}: each item as `toArray()`
     * with nested `location` removed and `location_label` set from the loaded relation.
     *
     * @param  Collection<int, Item>  $items
     * @return SupportCollection<int, array<string, mixed>>
     */
    public function mapItemsToCategorySelectJson(Collection $items): SupportCollection
    {
        $out = new SupportCollection();
        foreach ($items as $item) {
            if (! $item instanceof Item) {
                continue;
            }
            $row = $item->toArray();
            unset($row['location']);
            $row['location_label'] = $item->location?->localized_label;
            $out->push($row);
        }

        return $out;
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
        $itemCategoryId = $this->normalizeItemCategoryFilterId($categoryId);
        if ($itemCategoryId === null) {
            return new Collection();
        }

        $nameSql = TranslationDisplaySql::itemNameSubquerySql('items');

        $qb = Item::query()
            ->where('category_id', '=', $itemCategoryId)
            ->where('validation', true)
            ->with('location')
            ->select('items.id', 'items.location_id');
        SqlExpr::selectRaw($qb, "({$nameSql}) AS name");

        if ($query !== '') {
            SqlExpr::whereRaw($qb, "({$nameSql}) LIKE ?", ['%' . $query . '%']);
        }

        return $qb->limit(10)->get();
    }

    /**
     * @param  Collection<int, Item>  $items
     * @return SupportCollection<int, array{id: int, name: string, location_id: int, location_label: string|null}>
     */
    public function mapItemsForComponentAutocompleteJson(Collection $items): SupportCollection
    {
        $rows = [];
        foreach ($items as $item) {
            if (! $item instanceof Item) {
                continue;
            }
            $rows[] = [
                'id' => (int) $item->id,
                'name' => (string) $item->name,
                'location_id' => (int) $item->location_id,
                'location_label' => $item->location !== null
                    ? (string) $item->location->localized_label
                    : null,
            ];
        }

        return new SupportCollection($rows);
    }

    public function countValidatedByNameAndCategory(string $name, string $categoryId, ?int $languageId = null): int
    {
        $itemCategoryId = $this->normalizeItemCategoryFilterId($categoryId);
        if ($itemCategoryId === null) {
            return 0;
        }

        $langId = $languageId ?? Language::idForPreferredFormLocale();

        return Item::query()
            ->where('category_id', '=', $itemCategoryId)
            ->where('validation', true)
            ->whereHas('translations', function ($q) use ($name, $langId): void {
                $q->where('language_id', $langId)
                    ->where('name', '=', $name);
            })
            ->count();
    }

    public function createItemWithIdentificationCode(AdminStoreItemRequest $request): Item
    {
        $itemAttributes = [
            'date' => $request->input('date'),
            'category_id' => $request->input('category_id'),
            'location_id' => $request->input('location_id'),
            'collaborator_id' => $request->input('collaborator_id'),
            'validation' => $request->boolean('validation'),
            'identification_code' => '000',
        ];

        $translations = $request->validated('translations');

        $item = null;

        DB::transaction(function () use ($itemAttributes, $translations, &$item): void {
            $item = Item::create($itemAttributes);
            $item->syncTranslationsFromAdminForm($translations);
            $item->update([
                'identification_code' => $this->createIdentificationCode($item),
            ]);
        });

        if (! $item instanceof Item) {
            throw new RuntimeException('Item creation failed');
        }

        return $item;
    }

    /**
     * @param  int|null  $nameLanguageId  Translation row used for the title (e.g. contribution locale).
     *                                    Null uses {@see Language::idForPreferredFormLocale()}.
     */
    public function createIdentificationCode(Item $item, ?int $nameLanguageId = null): string
    {
        $item->loadMissing('location', 'translations');
        $langId = $nameLanguageId ?? Language::idForPreferredFormLocale();
        $translation = $item->translations->firstWhere('language_id', $langId);
        if ($translation === null || trim((string) $translation->name) === '') {
            $translation = $item->translations->sortBy('language_id')->first();
        }
        $title = $translation !== null ? (string) $translation->name : '';
        $locCode = $item->location?->code ?? 'INDEF';

        return ItemIdentificationCode::buildForItem($item, $title, $locCode, $item->created_at);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateItem(Item $item, array $attributes): void
    {
        $translations = $attributes['translations'] ?? [];
        $itemData = Arr::except($attributes, ['translations']);
        $split = TranslatablePayload::split($itemData, TranslatablePayload::ITEM_KEYS);

        if ($split['persist'] !== []) {
            $item->update($split['persist']);
        }

        if ($translations !== []) {
            $item->syncTranslationsFromAdminForm($translations);
        }

        $item->normalizeSingleCover();
    }

    public function deleteItem(Item $item): void
    {
        $item->delete();
    }

    /**
     * Strict id filter for `items.category_id` (admin selects, public JSON, autocomplete).
     * Admin item index text search uses `AdminIndexQueryBuilder` LIKE subqueries on translations, not this helper.
     */
    private function normalizeItemCategoryFilterId(string $itemCategoryId): ?int
    {
        if ($itemCategoryId === '' || ! ctype_digit($itemCategoryId)) {
            return null;
        }

        return (int) $itemCategoryId;
    }
}
