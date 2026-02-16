<?php

namespace App\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ItemIndexQueryBuilder
{
    /**
     * @return array{items: LengthAwarePaginator, sectionName: string}
     */
    public function build(Request $request): array
    {
        $query = $this->baseQuery();
        $order = (int) ($request->order ?: 1);
        $sectionId = $request->section ?? $request->input('section');

        $this->applySection($query, $sectionId);
        $this->applySearch($query, $request->search);
        $this->applyCategoryFilter($query, $request->category);
        $this->applyTagFilter($query, $request->tag);
        $this->applyOrder($query, $order);

        $items = $query->paginate(24)->withQueryString()->appends(['section' => $sectionId]);
        $sectionName = $this->resolveSectionName($sectionId);

        return ['items' => $items, 'sectionName' => $sectionName];
    }

    /**
     * @return Builder<Item>
     */
    private function baseQuery(): Builder
    {
        return Item::query()
            ->select('id', 'name', 'date', 'section_id', 'description', 'identification_code', 'image')
            ->where('validation', true);
    }

    /**
     * @param Builder<Item> $query
     */
    private function applySection(Builder $query, ?string $sectionId): void
    {
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
    }

    /**
     * @param Builder<Item> $query
     */
    private function applySearch(Builder $query, ?string $search): void
    {
        if (isset($search) && $search !== '') {
            $query->where('name', 'LIKE', "%{$search}%");
        }
    }

    /**
     * @param Builder<Item> $query
     * @param array<int|string>|null $categoryIds
     */
    private function applyCategoryFilter(Builder $query, $categoryIds): void
    {
        if (!isset($categoryIds) || $categoryIds === []) {
            return;
        }
        $query->whereHas('tags', function (Builder $tagRelationQuery) use ($categoryIds): void {
            $tagRelationQuery->whereIn('category_id', $categoryIds)->where('tag_item.validation', true);
        });
    }

    /**
     * @param Builder<Item> $query
     * @param array<int|string>|null $tagIds
     */
    private function applyTagFilter(Builder $query, $tagIds): void
    {
        if (!isset($tagIds) || $tagIds === []) {
            return;
        }
        $query->whereHas('tags', function (Builder $tagRelationQuery) use ($tagIds): void {
            $tagRelationQuery->whereIn('tag_id', $tagIds)->where('tag_item.validation', true);
        });
    }

    /**
     * @param Builder<Item> $query
     */
    private function applyOrder(Builder $query, int $order): void
    {
        $orderMap = [
            1 => ['date', 'asc'],
            2 => ['date', 'desc'],
            3 => ['name', 'asc'],
            4 => ['name', 'desc'],
        ];
        $orderSpec = $orderMap[$order] ?? $orderMap[1];
        $orderColumn = $orderSpec[0];
        $orderDirection = $orderSpec[1];
        $query->orderBy($orderColumn, $orderDirection);
    }

    private function resolveSectionName(?string $sectionId): string
    {
        if (!$sectionId) {
            return '';
        }
        $section = Section::find($sectionId);

        return $section !== null ? $section->name : '';
    }
}
