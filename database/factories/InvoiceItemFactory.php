<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 3);
        $price = fake()->numberBetween(25000, 200000);

        return [
            'invoice_id' => Invoice::factory(),
            'item_type' => fake()->randomElement(['consultation', 'action', 'medicine', 'room', 'admin']),
            'name_snapshot' => fake()->words(3, true),
            'qty' => $qty,
            'price_snapshot' => $price,
            'subtotal' => $qty * $price,
        ];
    }
}
