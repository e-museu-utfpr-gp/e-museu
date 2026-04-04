<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\{Item, ItemCategory, ItemImage};
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
            'date' => $this->faker->date,
            'identification_code' => 'FACTORY_' . $this->faker->unique()->uuid(),
            'validation' => $this->faker->boolean,
            'category_id' => ItemCategory::pluck('id')->random(),
            'collaborator_id' => Collaborator::pluck('id')->random(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Item $item): void {
            $item->syncPrimaryLocaleTranslation([
                'name' => 'Item ' . $this->faker->unique()->numerify('########'),
                'description' => $this->faker->paragraph,
                'history' => $this->faker->paragraph(500),
                'detail' => $this->faker->text,
            ]);
        });
    }

    /**
     * Create an item with multiple images (1 cover + N-1 gallery).
     *
     * @param  int  $total  Total number of images (default 3: 1 cover + 2 gallery)
     */
    public function withImages(int $total = 3): static
    {
        return $this->has(
            ItemImage::factory()->cover()->count(1)
        )->has(
            ItemImage::factory()->gallery()->count(max(0, $total - 1))
        );
    }
}
