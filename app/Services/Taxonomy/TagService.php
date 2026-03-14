<?php

namespace App\Services\Taxonomy;

use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    /**
     * @return Collection<int, Tag>
     */
    public function getByCategory(string $categoryId): Collection
    {
        return Tag::where('tag_category_id', 'LIKE', $categoryId)
            ->orderBy('name', 'asc')
            ->get();
    }

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
     * @return Collection<int, Tag>
     */
    public function getValidatedNamesForAutocomplete(string $query, string $categoryId): Collection
    {
        $qb = Tag::select('name')
            ->where('tag_category_id', 'LIKE', $categoryId)
            ->where('validation', true);

        if ($query !== '') {
            $qb = $qb->where('name', 'LIKE', '%' . $query . '%');
        }

        return $qb->limit(10)->get();
    }

    public function countValidatedByNameAndCategory(string $name, string $categoryId): int
    {
        return Tag::where('tag_category_id', 'LIKE', $categoryId)
            ->where('name', 'LIKE', $name)
            ->where('validation', true)
            ->count();
    }

    /**
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
