<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public $invoiceItems = [];

    public function run()
    {
        User::factory(5)->create();
        Supplier::insert(array_map(fn($i) => ['name' => 'Supplier ' . $i], range(1, 1)));
        Warehouse::insert(array_map(fn($i) => ['name' => 'Warehouse ' . $i], range(1, 2)));

        for ($i = 1; $i <= 100; $i++) {
            Item::factory()->create([
                'name' => 'Item ' . $i,
            ]);
        }

        // Create invoices with associated invoice items
        $invoices = Invoice::factory(1)->create()->each(function ($invoice) {
            InvoiceItem::factory()->count(1)->create(['invoice_id' => $invoice->id]);
        });

        // Create transactions and attach items to them with pivot data
        foreach ($invoices as $invoice) {
            $transaction = Transaction::create([
                'created_by' => 1,
                'destination_warehouse_id' => $invoice->warehouse_id,
                'source_warehouse_id' => null,
                'invoice_id' => $invoice->id,
                'transaction_type' => 'purchase',
            ]);

            // Attach items with quantity and expiration_date from the pivot table
            $itemsWithData = $invoice->items->mapWithKeys(function ($item) {
                return [
                    $item->id => [
                        'quantity' => $item->pivot->quantity,
                        'expiration_date' => $item->pivot->expiration_date,
                    ]
                ];
            });

            $transaction->items()->attach($itemsWithData->toArray());
        }
    }
}
