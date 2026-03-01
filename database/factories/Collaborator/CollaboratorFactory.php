<?php

namespace Database\Factories\Collaborator;

use App\Enums\CollaboratorRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Collaborator\Collaborator>
 */
class CollaboratorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->word,
            'contact' => $this->faker->unique()->email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
        ];
    }
}
