<?php

namespace Database\Factories;

use App\Models\Catalog\Item;
use App\Models\Proprietary\Proprietary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Catalog\Extra>
 */
class ExtraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::pluck('id')->random(),
            'proprietary_id' => Proprietary::pluck('id')->random(),
            'info' => $this->faker->paragraph,
            'validation' => $this->faker->boolean,
        ];
    }
}
