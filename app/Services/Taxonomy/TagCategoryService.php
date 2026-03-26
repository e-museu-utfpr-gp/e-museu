<?php

namespace App\Services\Taxonomy;

use App\Models\Taxonomy\TagCategory;
use App\Support\Admin\AdminIndexConfig;
use App\Support\Admin\AdminIndexQueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TagCategoryService
{
    /**
     * @return array{tagCategories: LengthAwarePaginator<int, TagCategory>, count: int}
     */
    public function getPaginatedTagCategoriesForAdminIndex(Request $request): array
    {
        $count = TagCategory::count();
        $query = TagCategory::query();

        AdminIndexQueryBuilder::build($query, $request, AdminIndexConfig::tagCategories());

        $tagCategories = $query->paginate(30)->withQueryString();

        return ['tagCategories' => $tagCategories, 'count' => $count];
    }

    /**
     * @return Collection<int, TagCategory>
     */
    public function getForIndex(): Collection
    {
        return TagCategory::select('name', 'id')->orderBy('name', 'asc')->get();
    }

    /**
     * @return Collection<int, TagCategory>
     */
    public function getForForm(): Collection
    {
        return TagCategory::orderBy('name', 'asc')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createTagCategory(array $data): TagCategory
    {
        return TagCategory::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateTagCategory(TagCategory $tagCategory, array $data): void
    {
        $tagCategory->update($data);
    }

    public function deleteTagCategory(TagCategory $tagCategory): void
    {
        $tagCategory->delete();
    }
}
