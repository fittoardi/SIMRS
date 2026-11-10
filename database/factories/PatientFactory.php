<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'medical_record_number' => 'RM-' . fake()->unique()->numerify('2611-####'),
            'nik' => fake()->unique()->numerify('3171############'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_date' => fake()->date('Y-m-d', '-20 years'),
            'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
            'allergies' => null,
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'bpjs_number' => fake()->numerify('0001#########'),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'emergency_contact_relation' => 'Keluarga',
        ];
    }
}

