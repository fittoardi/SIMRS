<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientFamilyMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientFamilyMember>
 */
class PatientFamilyMemberFactory extends Factory
{
    protected $model = PatientFamilyMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'patient_id' => Patient::factory(),
            'relationship' => fake()->randomElement(['diri_sendiri', 'suami', 'istri', 'anak', 'orang_tua', 'kerabat']),
        ];
    }
}
