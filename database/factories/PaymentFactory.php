<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'provider' => 'cash',
            'provider_ref' => 'CASH-' . strtoupper(fake()->bothify('##??####')),
            'method' => 'cash',
            'amount' => 150000,
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
            'raw_payload' => null,
        ];
    }
}
