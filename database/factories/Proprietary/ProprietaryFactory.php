<?php

namespace Database\Factories\Proprietary;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Proprietary\Proprietary>
 */
class ProprietaryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->word,
            'contact' => $this->faker->unique()->email
        ];
    }
}
