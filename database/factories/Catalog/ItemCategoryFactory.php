<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\ItemCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Catalog\ItemCategory>
 */
class ItemCategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ItemCategory $category): void {
            $category->syncPrimaryLocaleTranslation([
                'name' => $this->faker->unique()->word,
            ]);
        });
    }
}
