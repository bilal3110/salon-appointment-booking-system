<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_name' => fake()->unique()->words(2, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 15, 250),
            'service_type' => fake()->randomElement(['men', 'women']),
        ];
    }
}
