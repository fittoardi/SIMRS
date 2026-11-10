<?php

namespace Database\Factories;

use App\Enums\QueueStatus;
use App\Models\DoctorSchedule;
use App\Models\Queue;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Queue>
 */
class QueueFactory extends Factory
{
    protected $model = Queue::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'doctor_schedule_id' => DoctorSchedule::factory(),
            'queue_number' => fake()->numberBetween(1, 30),
            'queue_code' => 'POL-'.date('ymd').'-'.fake()->numerify('###'),
            'qr_token' => 'TKT-'.strtoupper(fake()->bothify('????????????')),
            'estimated_time' => '09:00',
            'status' => QueueStatus::Waiting,
        ];
    }
}
