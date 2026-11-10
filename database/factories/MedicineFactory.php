<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medicine>
 */
class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition(): array
    {
        return [
            'sku' => 'OBT-'.fake()->unique()->numerify('####'),
            'name' => fake()->words(2, true).' 500mg',
            'category' => fake()->randomElement(['Analgesik', 'Antibiotik', 'Antipiretik', 'Vitamin']),
            'unit' => fake()->randomElement(['Tablet', 'Kapsul', 'Sirup', 'Ampul']),
            'price' => fake()->randomElement([5000, 10000, 15000, 25000]),
            'stock' => 100,
            'min_stock' => 10,
            'expired_at' => now()->addYear(),
            'is_active' => true,
        ];
    }
}
