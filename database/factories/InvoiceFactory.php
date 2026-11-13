<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'number' => 'INV-' . date('Ym') . '-' . fake()->unique()->numerify('####'),
            'visit_id' => Visit::factory(),
            'patient_id' => Patient::factory(),
            'subtotal' => 150000,
            'discount' => 0,
            'total' => 150000,
            'status' => PaymentStatus::Unpaid,
            'payment_status' => 'unpaid',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
