<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonials>
 */
class TestimonialsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'user_profession' => fake()->jobTitle(),
            'message' => fake()->paragraph(),
            'rating' => fake()->numberBetween(3, 5),
        ];
    }
}
