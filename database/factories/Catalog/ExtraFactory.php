<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\{Extra, Item};
use App\Models\Collaborator\Collaborator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Catalog\Extra>
 */
class ExtraFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::pluck('id')->random(),
            'collaborator_id' => Collaborator::pluck('id')->random(),
            'validation' => $this->faker->boolean,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Extra $extra): void {
            $extra->syncPrimaryLocaleTranslation([
                'info' => $this->faker->paragraph,
            ]);
        });
    }
}
