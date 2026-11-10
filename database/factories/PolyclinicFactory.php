<?php

namespace Database\Factories;

use App\Models\Polyclinic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Polyclinic>
 */
class PolyclinicFactory extends Factory
{
    protected $model = Polyclinic::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => 'Poli '.ucfirst($name),
            'slug' => Str::slug('poli-'.$name),
            'code' => strtoupper(fake()->unique()->lexify('P??')),
            'description' => fake()->sentence(),
            'location' => 'Gedung A Lantai '.fake()->numberBetween(1, 3),
            'is_active' => true,
        ];
    }
}
