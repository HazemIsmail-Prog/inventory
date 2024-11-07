<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchasingInvoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => 1, // Get a random existing category ID
            'supplier_id' => Supplier::all()->random()->id, // Get a random existing category ID
            'warehouse_id' => Warehouse::all()->random()->id, // Get a random existing category ID
            'invoice_date' => $this->faker->date,
            'total_amount' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
