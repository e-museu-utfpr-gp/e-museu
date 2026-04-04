<?php

namespace Database\Factories\Taxonomy;

use App\Models\Taxonomy\Tag;
use App\Models\Taxonomy\TagCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Taxonomy\Tag>
 */
class TagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_category_id' => fn () => TagCategory::query()->inRandomOrder()->value('id'),
            'validation' => $this->faker->boolean,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Tag $tag): void {
            $tag->syncPrimaryLocaleTranslation([
                'name' => $this->faker->unique()->word,
            ]);
        });
    }
}
