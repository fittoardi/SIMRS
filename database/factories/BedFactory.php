<?php

namespace Database\Factories;

use App\Enums\BedStatus;
use App\Models\Bed;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bed>
 */
class BedFactory extends Factory
{
    protected $model = Bed::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'bed_number' => 'BED-'.fake()->unique()->numerify('####'),
            'status' => BedStatus::Available,
            'current_visit_id' => null,
        ];
    }

    /**
     * State bed occupied.
     */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BedStatus::Occupied,
        ]);
    }
}
