<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemComponentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'component_id' => Item::pluck('id')->random(),
            'item_id' => Item::pluck('id')->random()
        ];
    }
}
