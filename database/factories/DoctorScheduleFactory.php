<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorSchedule>
 */
class DoctorScheduleFactory extends Factory
{
    protected $model = DoctorSchedule::class;

    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'day_of_week' => fake()->numberBetween(1, 6),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'max_quota' => 20,
            'room_name' => 'Ruang Poliklinik '.fake()->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
