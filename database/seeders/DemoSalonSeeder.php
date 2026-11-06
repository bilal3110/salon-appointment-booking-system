<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Testimonials;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSalonSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@salon.test'],
            User::factory()->admin()->make([
                'name' => 'Salon Demo Admin',
                'email' => 'admin@salon.test',
                'phone' => '555-0100',
            ])->getAttributes()
        );

        $serviceData = [
            ['Classic Haircut', 'A tailored cut and finish.', 35, 'men'],
            ['Beard Trim', 'Precision shaping and hot towel finish.', 22, 'men'],
            ["Men's Facial", 'A refreshing cleanse and facial treatment.', 55, 'men'],
            ['Hair Coloring', 'Custom color consultation and service.', 95, 'women'],
            ['Blowout Styling', 'A polished style for any occasion.', 45, 'women'],
            ['Manicure', 'Nail shaping, cuticle care, and polish.', 30, 'women'],
            ['Classic Pedicure', 'Relaxing foot care and polish.', 42, 'women'],
            ['Makeup Session', 'Professional makeup for your event.', 85, 'women'],
        ];

        $services = collect($serviceData)->mapWithKeys(function (array $data) {
            [$name, $description, $price, $type] = $data;

            return [$name => Service::firstOrCreate(
                ['service_name' => $name],
                [
                    'description' => $description,
                    'price' => $price,
                    'service_type' => $type,
                ]
            )];
        });

        $staffData = [
            ['Ava Bennett', 'ava.bennett@salon.test', ['Classic Haircut', 'Beard Trim', "Men's Facial"]],
            ['Noah Williams', 'noah.williams@salon.test', ['Classic Haircut', 'Beard Trim']],
            ['Mia Johnson', 'mia.johnson@salon.test', ['Hair Coloring', 'Blowout Styling', 'Makeup Session']],
            ['Olivia Chen', 'olivia.chen@salon.test', ['Hair Coloring', 'Manicure', 'Classic Pedicure']],
            ['Ethan Brooks', 'ethan.brooks@salon.test', ['Classic Haircut', "Men's Facial"]],
            ['Sofia Martinez', 'sofia.martinez@salon.test', ['Blowout Styling', 'Manicure', 'Classic Pedicure', 'Makeup Session']],
        ];

        $staffMembers = collect($staffData)->map(function (array $data) use ($services) {
            [$name, $email, $serviceNames] = $data;
            $user = User::firstOrCreate(
                ['email' => $email],
                User::factory()->make([
                    'name' => $name,
                    'email' => $email,
                    'type' => 'staff',
                ])->getAttributes()
            );

            $staff = Staff::where('user_id', $user->id)->first();
            if (!$staff) {
                $staff = Staff::factory()->for($user)->create();
            }

            $staff->services()->syncWithoutDetaching(
                collect($serviceNames)->map(fn (string $serviceName) => $services[$serviceName]->id)->all()
            );

            return $staff;
        });

        $statuses = ['pending', 'pending', 'confirmed', 'completed', 'canceled'];
        for ($index = 1; $index <= 25; $index++) {
            $staff = $staffMembers[($index - 1) % $staffMembers->count()];
            $service = $staff->services()->inRandomOrder()->first();
            $email = sprintf('demo.customer%02d@example.test', $index);

            if (!Booking::where('customer_email', $email)->exists()) {
                Booking::factory()
                    ->for($staff, 'staff')
                    ->for($service, 'service')
                    ->create([
                        'customer_email' => $email,
                        'booking_date' => now()->startOfDay()->addDays(($index % 21) - 7)->toDateString(),
                        'booking_time' => sprintf('%02d:00', 9 + ($index % 9)),
                        'status' => $statuses[($index - 1) % count($statuses)],
                    ]);
            }
        }

        $testimonials = [
            ['Grace Turner', 'Teacher', 'A wonderful experience from start to finish. The team really listened to what I wanted.', 5],
            ['Daniel Reed', 'Designer', 'Excellent service and a great atmosphere. I will definitely be back.', 5],
            ['Priya Shah', 'Accountant', 'Friendly staff and a beautiful result. Booking was simple and quick.', 4],
            ['Ella Foster', 'Photographer', 'The stylist was thoughtful and attentive. I love my new look.', 5],
            ['Lucas Price', 'Chef', 'Professional, relaxing, and exactly what I needed.', 4],
        ];

        foreach ($testimonials as [$name, $profession, $message, $rating]) {
            Testimonials::firstOrCreate(
                ['name' => $name],
                Testimonials::factory()->make([
                    'name' => $name,
                    'user_profession' => $profession,
                    'message' => $message,
                    'rating' => $rating,
                ])->getAttributes()
            );
        }

        $this->command?->info('Demo salon data is ready. Login: admin@salon.test / password');
    }
}
