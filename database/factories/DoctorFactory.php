<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Polyclinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'user_id' => User::factory(),
            'polyclinic_id' => Polyclinic::factory(),
            'name' => 'dr. ' . $name . ', Sp.PD',
            'slug' => Str::slug('dr-' . $name),
            'specialization' => 'Spesialis Penyakit Dalam',
            'sip_number' => fake()->numerify('SIP/###/####/2026'),
            'consultation_fee' => 150000,
            'bio' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
