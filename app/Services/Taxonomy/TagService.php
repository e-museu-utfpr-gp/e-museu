<?php

namespace App\Services\Taxonomy;

use App\Models\Taxonomy\Tag;
use App\Support\AdminIndexConfig;
use App\Support\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TagService
{
    /**
     * @return array{tags: LengthAwarePaginator<int, Tag>, count: int}
     */
    public function getPaginatedTagsForAdminIndex(Request $request): array
    {
        $count = Tag::count();
        $query = Tag::query();
        $query->leftJoin('tag_categories', 'tags.tag_category_id', '=', 'tag_categories.id');
        $query->select([
            'tags.*',
            'tags.name AS tag_name',
            'tags.created_at AS tag_created',
            'tags.updated_at AS tag_updated',
            'tag_categories.name AS category_name',
        ]);

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::tags());

        $tags = $query->paginate(30)->withQueryString();

        return ['tags' => $tags, 'count' => $count];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createFromAdminRequestData(array $data): Tag
    {
        $data['tag_category_id'] = $data['category_id'];
        unset($data['category_id']);

        return Tag::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateFromAdminRequestData(Tag $tag, array $data): void
    {
        $data['tag_category_id'] = $data['category_id'];
        unset($data['category_id']);

        $tag->update($data);
    }

    public function deleteTag(Tag $tag): void
    {
        $tag->delete();
    }

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
