<?php

namespace App\Support\Catalog;

use App\Models\Catalog\Item;
use App\Support\Content\TranslationDisplaySql;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Builds the query for the public catalog item index (validated items, filters and sort). Caller paginates.
 */
class ItemIndexQueryBuilder
{
    /**
     * Build and return the query with filters and sort applied. Caller is responsible for paginating.
     *
     * @return Builder<Item>
     */
    public static function build(Request $request): Builder
    {
        $query = Item::query()
            ->with(['coverImage', 'itemCategory.translations.language'])
            ->where('validation', true)
            ->select('items.*')
            ->addSelect(TranslationDisplaySql::itemCatalogListSelectAliases());

        $sortOption = (int) $request->input('order', 1);
        $itemCategoryId = $request->item_category ?? $request->input('item_category');

        self::applyItemCategoryFilter($query, $itemCategoryId);
        self::applySearchFilter($query, $request->search);
        self::applyTagCategoryFilter($query, $request->category);
        self::applyTagFilter($query, $request->tag);
        self::applySort($query, $sortOption);

        return $query;
    }

    /**
     * @param  Builder<Item>  $query
     */
    private static function applyItemCategoryFilter(Builder $query, ?string $itemCategoryId): void
    {
        if ($itemCategoryId) {
            $query->where('category_id', $itemCategoryId);
        }
    }

    /**
     * @param  Builder<Item>  $query
     */
    private static function applySearchFilter(Builder $query, ?string $searchTerm): void
    {
        if (isset($searchTerm) && $searchTerm !== '') {
            $needle = '%' . $searchTerm . '%';
            $nameSql = TranslationDisplaySql::itemNameSubquerySql('items');
            $descSql = TranslationDisplaySql::itemTranslationSubquerySql('description', 'items');
            $query->where(function (Builder $q) use ($needle, $nameSql, $descSql): void {
                $q->whereRaw("({$nameSql}) LIKE ?", [$needle])
                    ->orWhereRaw("({$descSql}) LIKE ?", [$needle]);
            });
        }
    }

    /**
     * @param  Builder<Item>  $query
     * @param  array<int|string>|null  $tagCategoryIds
     */
    private static function applyTagCategoryFilter(Builder $query, $tagCategoryIds): void
    {
        if (! isset($tagCategoryIds) || $tagCategoryIds === []) {
            return;
        }
        $query->whereHas('tags', function (Builder $tagsRelationQuery) use ($tagCategoryIds): void {
            $tagsRelationQuery->whereIn('tag_category_id', $tagCategoryIds)->where('item_tag.validation', true);
        });
    }

    /**
     * @param  Builder<Item>  $query
     * @param  array<int|string>|null  $tagIds
     */
    private static function applyTagFilter(Builder $query, $tagIds): void
    {
        if (! isset($tagIds) || $tagIds === []) {
            return;
        }
        $query->whereHas('tags', function (Builder $tagsRelationQuery) use ($tagIds): void {
            $tagsRelationQuery->whereIn('tag_id', $tagIds)->where('item_tag.validation', true);
        });
    }

    /**
     * @param  Builder<Item>  $query
     */
    private static function applySort(Builder $query, int $sortOption): void
    {
        $sortOptionMap = [
            1 => ['date', 'asc'],
            2 => ['date', 'desc'],
            3 => ['name', 'asc'],
            4 => ['name', 'desc'],
        ];
        [$sortColumn, $sortDirection] = $sortOptionMap[$sortOption] ?? $sortOptionMap[1];
        if ($sortColumn === 'name') {
            $expr = TranslationDisplaySql::itemNameExpression();
            $query->orderBy($expr, $sortDirection);

            return;
        }

        $query->orderBy('items.' . $sortColumn, $sortDirection);
    }
}
