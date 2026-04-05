<?php

namespace Database\Factories\Collaborator;

use App\Enums\Collaborator\CollaboratorRole;
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
            'email' => $this->faker->unique()->safeEmail(),
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ];
    }
}
