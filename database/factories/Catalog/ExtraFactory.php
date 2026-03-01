<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Item;
use App\Models\Collaborator\Collaborator;
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
            'collaborator_id' => Collaborator::pluck('id')->random(),
            'info' => $this->faker->paragraph,
            'validation' => $this->faker->boolean,
        ];
    }
}
