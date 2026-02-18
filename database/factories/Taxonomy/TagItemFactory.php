<?php

namespace Database\Factories\Taxonomy;

use App\Models\Catalog\Item;
use App\Models\Taxonomy\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Catalog\TagItem>
 */
class TagItemFactory extends Factory
{
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
