<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchasingInvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'invoice_id' => Invoice::all()->random()->id,
            'item_id' => Item::all()->random()->id,
            'quantity' => $this->faker->numberBetween(1, 100),
            'price_per_unit' => $this->faker->randomFloat(2, 5, 50),
            'expiration_date' => $this->faker->dateTimeBetween('+1 week', '+2 years')->format('Y-m-d'), // Future date only

        ];
    }
}
