<?php

namespace App\Services\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemTag;
use App\Services\Taxonomy\TagService;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ItemTagService
{
    /**
     * @return array{itemTags: LengthAwarePaginator<int, ItemTag>, count: int}
     */
    public function getPaginatedItemTagsForAdminIndex(Request $request): array
    {
        $count = ItemTag::count();
        $query = ItemTag::query()->forAdminList();

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::itemTags());

        $itemTags = $query->paginate(50)->withQueryString();

        return ['itemTags' => $itemTags, 'count' => $count];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createItemTag(array $data): ItemTag
    {
        return ItemTag::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateItemTag(ItemTag $itemTag, array $data): void
    {
        $itemTag->update($data);
    }

    public function deleteItemTag(ItemTag $itemTag): void
    {
        $itemTag->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $tagsData
     */
    public function attachTagsToItem(
        Item $item,
        array $tagsData,
        TagService $tagService,
        ?int $contentLanguageId = null
    ): void {
        $ids = [];
        foreach ($tagsData as $tagData) {
            $ids[] = $tagService->findOrCreate($tagData, $contentLanguageId)->id;
        }
        $item->tags()->syncWithoutDetaching(array_values(array_unique($ids)));
    }
}
