<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $code = strtoupper($this->faker->unique()->lexify('?????'));

        return [
            'name' => 'Location ' . $code,
            'code' => $code,
        ];
    }

    /**
     * Fixed {@see Location::$code} for tests (uppercased; name is generic).
     */
    public function withLocationCode(string $code): static
    {
        $upper = strtoupper(trim($code));

        return $this->state(fn (array $attributes) => [
            'code' => $upper,
            'name' => $attributes['name'] ?? ('Test location ' . $upper),
        ]);
    }
}
