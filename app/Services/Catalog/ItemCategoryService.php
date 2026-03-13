<?php

namespace App\Services\Catalog;

use App\Models\Catalog\ItemCategory;
use Illuminate\Database\Eloquent\Collection;

class ItemCategoryService
{
    /**
     * @return Collection<int, ItemCategory>
     */
    public function getForIndex(): Collection
    {
        return ItemCategory::select('name', 'id')->orderBy('name', 'asc')->get();
    }

    /**
     * All item categories ordered by name (e.g. for admin form dropdowns).
     *
     * @return Collection<int, ItemCategory>
     */
    public function getForForm(): Collection
    {
        return ItemCategory::orderBy('name')->get();
    }
}
