<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Catalog\ItemComponent>
 */
class ItemComponentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'component_id' => Item::pluck('id')->random(),
            'item_id' => Item::pluck('id')->random(),
        ];
    }
}
