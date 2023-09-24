<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'height' => fake()->unique()->randomNumber(),
            'weight' => fake()->unique()->randomNumber(),
            'length' => fake()->unique()->randomNumber(),
            'width' => fake()->unique()->randomNumber(),
        ];
    }
}
