<?php

declare(strict_types=1);

namespace Database\Factories\Catalog;

use App\Enums\Catalog\ItemImageType;
use App\Models\Catalog\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Catalog\ItemImage>
 */
class ItemImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'path' => function (array $attributes) {
                $itemId = $attributes['item_id'] ?? 0;

                return sprintf('items/%s/%s.png', $itemId, Str::uuid());
            },
            'type' => $this->faker->randomElement(['cover', 'gallery']),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }

    public function cover(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ItemImageType::COVER,
            'sort_order' => 0,
        ]);
    }

    public function gallery(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ItemImageType::GALLERY,
            'sort_order' => $attributes['sort_order'] ?? $this->faker->numberBetween(1, 50),
        ]);
    }
}
