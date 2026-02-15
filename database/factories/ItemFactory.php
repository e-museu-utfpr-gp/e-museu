<?php

namespace Database\Factories;

use App\Models\Catalog\Section;
use App\Models\Proprietary\Proprietary;
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
            'image' => $this->faker->imageUrl(500, 500),
            'validation' => $this->faker->boolean,
            'section_id' => Section::pluck('id')->random(),
            'proprietary_id' => Proprietary::pluck('id')->random(),
        ];
    }
}
