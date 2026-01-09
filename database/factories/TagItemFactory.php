<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Tag;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
            'item_id' => Item::pluck('id')->random()
        ];
    }
}
