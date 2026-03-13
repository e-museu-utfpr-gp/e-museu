<?php

namespace App\Services\Taxonomy;

use App\Models\Taxonomy\TagCategory;
use Illuminate\Database\Eloquent\Collection;

class TagCategoryService
{
    /**
     * @return Collection<int, TagCategory>
     */
    public function getForIndex(): Collection
    {
        return TagCategory::select('name', 'id')->orderBy('name', 'asc')->get();
    }
}
