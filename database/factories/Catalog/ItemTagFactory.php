<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Item;
use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Catalog\ItemTag>
 */
class ItemTagFactory extends Factory
{
    protected $model = \App\Models\Catalog\ItemTag::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_id' => Tag::pluck('id')->random(),
            'item_id' => Item::pluck('id')->random(),
        ];
    }
}
