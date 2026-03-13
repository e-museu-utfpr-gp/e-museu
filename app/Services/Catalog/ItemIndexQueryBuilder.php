<?php

namespace App\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ItemIndexQueryBuilder
{
    /**
     * @return array{items: LengthAwarePaginator, categoryName: string}
     */
    public function build(Request $request): array
    {
        $query = $this->baseQuery();
        $sortOption = (int) ($request->order ?: 1);
        $itemCategoryId = $request->item_category ?? $request->input('item_category');

        $this->applyItemCategoryFilter($query, $itemCategoryId);
        $this->applySearchFilter($query, $request->search);
        $this->applyTagCategoryFilter($query, $request->category);
        $this->applyTagFilter($query, $request->tag);
        $this->applySort($query, $sortOption);

        $items = $query->paginate(24)->withQueryString()->appends(['item_category' => $itemCategoryId]);
        $itemCategoryName = $this->resolveItemCategoryName($itemCategoryId);

        return ['items' => $items, 'categoryName' => $itemCategoryName];
    }

    /**
     * @return Builder<Item>
     */
    private function baseQuery(): Builder
    {
        return Item::query()
            ->with('coverImage')
            ->select('id', 'name', 'date', 'category_id', 'description', 'identification_code')
            ->where('validation', true);
    }

    /**
     * @param  Builder<Item>  $query
     */
    private function applyItemCategoryFilter(Builder $query, ?string $itemCategoryId): void
    {
        if ($itemCategoryId) {
            $query->where('category_id', $itemCategoryId);
        }
    }

    /**
     * @param  Builder<Item>  $query
     */
    private function applySearchFilter(Builder $query, ?string $searchTerm): void
    {
        if (isset($searchTerm) && $searchTerm !== '') {
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }
    }

    /**
     * Filter items that have at least one tag in the given tag categories.
     *
     * @param  Builder<Item>  $query
     * @param  array<int|string>|null  $tagCategoryIds
     */
    private function applyTagCategoryFilter(Builder $query, $tagCategoryIds): void
    {
        if (! isset($tagCategoryIds) || $tagCategoryIds === []) {
            return;
        }
        $query->whereHas('tags', function (Builder $tagsRelationQuery) use ($tagCategoryIds): void {
            $tagsRelationQuery->whereIn('tag_category_id', $tagCategoryIds)->where('item_tag.validation', true);
        });
    }

    /**
     * Filter items that have at least one of the given tags.
     *
     * @param  Builder<Item>  $query
     * @param  array<int|string>|null  $tagIds
     */
    private function applyTagFilter(Builder $query, $tagIds): void
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
    private function applySort(Builder $query, int $sortOption): void
    {
        $sortOptionMap = [
            1 => ['date', 'asc'],
            2 => ['date', 'desc'],
            3 => ['name', 'asc'],
            4 => ['name', 'desc'],
        ];
        [$sortColumn, $sortDirection] = $sortOptionMap[$sortOption] ?? $sortOptionMap[1];
        $query->orderBy($sortColumn, $sortDirection);
    }

    private function resolveItemCategoryName(?string $itemCategoryId): string
    {
        if (! $itemCategoryId) {
            return '';
        }
        $itemCategory = ItemCategory::find($itemCategoryId);

        return $itemCategory !== null ? $itemCategory->name : '';
    }
}
