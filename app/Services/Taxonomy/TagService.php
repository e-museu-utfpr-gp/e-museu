<?php

namespace App\Services\Taxonomy;

use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    /**
     * @return Collection<int, Tag>
     */
    public function getValidatedByCategory(string $categoryId): Collection
    {
        return Tag::select('name', 'id')
            ->where('validation', true)
            ->where('tag_category_id', $categoryId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Find a tag by category and name, or create it.
     *
     * @param  array<string, mixed>  $tagData  Must contain 'name'; use 'tag_category_id' or 'category_id'.
     */
    public function findOrCreate(array $tagData): Tag
    {
        $tagCategoryId = $tagData['tag_category_id'] ?? $tagData['category_id'] ?? null;
        $tag = Tag::where('tag_category_id', '=', $tagCategoryId)
            ->where('name', '=', $tagData['name'])
            ->first();

        if ($tag !== null) {
            return $tag;
        }

        $createData = $tagData;
        $createData['tag_category_id'] = $tagCategoryId;
        unset($createData['category_id']);

        return Tag::create($createData);
    }
}
