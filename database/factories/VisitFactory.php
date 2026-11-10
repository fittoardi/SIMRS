<?php

namespace Database\Factories;

use App\Enums\VisitStatus;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Polyclinic;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        return [
            'visit_number' => 'VIS-'.date('Ymd').'-'.fake()->unique()->numerify('####'),
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'polyclinic_id' => Polyclinic::factory(),
            'visit_date' => fake()->date('Y-m-d', '+7 days'),
            'visit_type' => 'outpatient',
            'payment_type' => 'general',
            'status' => VisitStatus::Registered,
            'notes' => fake()->sentence(),
        ];
    }
}
