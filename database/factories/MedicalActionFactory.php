<?php

namespace Database\Factories;

use App\Models\MedicalAction;
use App\Models\Polyclinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalAction>
 */
class MedicalActionFactory extends Factory
{
    protected $model = MedicalAction::class;

    public function definition(): array
    {
        return [
            'polyclinic_id' => Polyclinic::factory(),
            'code' => 'TDK-'.fake()->unique()->numerify('####'),
            'name' => 'Pemeriksaan / Tindakan '.fake()->words(2, true),
            'price' => fake()->randomElement([25000, 50000, 75000, 100000]),
            'is_active' => true,
        ];
    }
}
