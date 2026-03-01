<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Catalog\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->sentence,
            'description' => $this->faker->paragraph,
            'history' => $this->faker->paragraph(500),
            'detail' => $this->faker->text,
            'date' => $this->faker->date,
            'identification_code' => $this->faker->unique()->numberBetween(1, 1000),
            'image' => null,
            'validation' => $this->faker->boolean,
            'category_id' => ItemCategory::pluck('id')->random(),
            'collaborator_id' => Collaborator::pluck('id')->random(),
        ];
    }
}
