<?php

namespace Database\Factories;

use App\Enums\PrescriptionStatus;
use App\Models\Doctor;
use App\Models\Prescription;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'doctor_id' => Doctor::factory(),
            'pharmacist_id' => null,
            'status' => PrescriptionStatus::Pending,
            'notes' => fake()->sentence(),
        ];
    }
}
