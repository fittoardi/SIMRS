<?php

namespace Database\Factories;

use App\Models\MedicalAction;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecordAction>
 */
class MedicalRecordActionFactory extends Factory
{
    protected $model = MedicalRecordAction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = 1;
        $price = fake()->numberBetween(35000, 250000);

        return [
            'medical_record_id' => MedicalRecord::factory(),
            'medical_action_id' => MedicalAction::factory(),
            'action_name_snapshot' => fake()->words(3, true),
            'price_snapshot' => $price,
            'qty' => $qty,
            'subtotal' => $qty * $price,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
