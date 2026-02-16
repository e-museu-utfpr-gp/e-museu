<?php

namespace Database\Factories\Taxonomy;

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
            'category_id' => $this->faker->numberBetween(1, 4),
            'name' => $this->faker->unique()->word,
            'validation' => $this->faker->boolean,
        ];
    }
}
