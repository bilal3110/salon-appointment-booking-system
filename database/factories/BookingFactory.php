<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'service_id' => Service::factory(),
            'customer_name' => fake()->name(),
            'customer_contact' => fake()->numerify('###-###-####'),
            'customer_email' => fake()->unique()->safeEmail(),
            'booking_date' => fake()->dateTimeBetween('-14 days', '+30 days')->format('Y-m-d'),
            'booking_time' => fake()->randomElement([
                '09:00',
                '10:00',
                '11:00',
                '12:00',
                '13:00',
                '14:00',
                '15:00',
                '16:00',
            ]),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'canceled']),
        ];
    }
}
