<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $class = fake()->randomElement(['VVIP', 'VIP', 'Kelas 1', 'Kelas 2', 'Kelas 3', 'ICU']);
        $prices = [
            'VVIP' => 1200000,
            'VIP' => 800000,
            'Kelas 1' => 450000,
            'Kelas 2' => 300000,
            'Kelas 3' => 150000,
            'ICU' => 1500000,
        ];

        return [
            'code' => 'R-'.strtoupper(fake()->unique()->bothify('??-###')),
            'name' => 'Kamar '.fake()->word().' '.fake()->numerify('###'),
            'ward_name' => 'Bangsal '.fake()->word().' ('.$class.')',
            'class_type' => $class,
            'price_per_day' => $prices[$class] ?? 450000,
            'is_active' => true,
        ];
    }
}
