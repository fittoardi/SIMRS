<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'doctor_id' => Doctor::factory(),
            'nurse_id' => null,
            'blood_pressure' => '120/80',
            'heart_rate' => 78,
            'respiratory_rate' => 18,
            'temperature' => 36.6,
            'weight' => 65.0,
            'height' => 170.0,
            'subjective' => 'Pasien mengeluh demam dan batuk berdahak sejak 3 hari yang lalu.',
            'objective' => 'Keadaan umum tampak sakit sedang, faring hiperemis, ronki basah halus minimal.',
            'assessment' => 'Infeksi Saluran Pernapasan Akut (ISPA)',
            'icd10_code' => 'J06.9',
            'icd10_description' => 'Acute upper respiratory infection, unspecified',
            'plan' => 'Istirahat cukup, perbanyak cairan, terapi simptomatis.',
            'notes' => 'Kontrol kembali jika demam berlanjut lebih dari 3 hari.',
        ];
    }
}
