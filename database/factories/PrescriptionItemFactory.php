<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrescriptionItem>
 */
class PrescriptionItemFactory extends Factory
{
    protected $model = PrescriptionItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 5);
        $price = fake()->numberBetween(5000, 50000);

        return [
            'prescription_id' => Prescription::factory(),
            'medicine_id' => Medicine::factory(),
            'medicine_name_snapshot' => fake()->words(2, true).' 500mg',
            'qty' => $qty,
            'dosage' => fake()->randomElement(['3x1 tablet sesudah makan', '2x1 tablet sesudah makan', '1x1 tablet sebelum tidur']),
            'price_snapshot' => $price,
            'subtotal' => $qty * $price,
        ];
    }
}
