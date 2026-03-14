<?php

namespace App\Services\Catalog;

use App\Models\Catalog\Item;
use App\Services\Taxonomy\TagService;

class ItemTagService
{
    /**
     * @param  array<int, array<string, mixed>>  $tagsData
     */
    public function attachTagsToItem(Item $item, array $tagsData, TagService $tagService): void
    {
        foreach ($tagsData as $tagData) {
            $tag = $tagService->findOrCreate($tagData);
            $item->tags()->attach($tag->id);
        }
    }
}
