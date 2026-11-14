<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\SickLeaveCertificate;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SickLeaveCertificate>
 */
class SickLeaveCertificateFactory extends Factory
{
    protected $model = SickLeaveCertificate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::today();
        $days = fake()->numberBetween(1, 5);
        $endDate = $startDate->copy()->addDays($days - 1);

        return [
            'medical_record_id' => MedicalRecord::factory(),
            'visit_id' => Visit::factory(),
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'certificate_number' => 'SK/' . date('Ym') . '/' . fake()->unique()->numerify('####'),
            'days_count' => $days,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'diagnosis_note' => fake()->randomElement(['Dyspepsia Akut', 'Faringitis Akut', 'Demam Tifoid', 'Cephalgia / Sakit Kepala']),
            'notes' => 'Perlu istirahat medis tirah baring / bed rest di rumah.',
            'verification_token' => SickLeaveCertificate::generateVerificationToken(),
            'issued_at' => now(),
        ];
    }
}
