<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoiceForm extends Form
{
    public $id;
    public string|null $invoice_number;
    public $invoice_date;
    public string|null $notes;
    public int|null $supplier_id;
    public int|null $warehouse_id;
    public float|null $total_amount;
    public array $items = [];


    public function save()
    {

        DB::transaction(function () {

            $invoice = Auth::user()->invoices()->create($this->except('id', 'items'));

            // Consolidate items by summing quantities for duplicates with the same item_id and expiration_date
            $invoiceItemsData = collect($this->items)
                ->groupBy(function ($item) {
                    return $item['item_id'] . '-' . $item['expiration_date'] . '-' . $item['price_per_unit'];
                })
                ->map(function ($groupedItems) {
                    // Sum quantities for grouped items
                    return [
                        'item_id' => $groupedItems->first()['item_id'],
                        'quantity' => $groupedItems->sum('quantity'),
                        'expiration_date' => $groupedItems->first()['expiration_date'],
                        'price_per_unit' => $groupedItems->first()['price_per_unit'],
                    ];
                });

            // Attach each consolidated item individually
            foreach ($invoiceItemsData as $item) {
                $invoice->items()->attach($item['item_id'], [
                    'quantity' => $item['quantity'],
                    'expiration_date' => $item['expiration_date'],
                    'price_per_unit' => $item['price_per_unit'],
                ]);
            }

            $transaction = Auth::user()->transactions()->create([
                'invoice_id' => $invoice->id,
                'destination_warehouse_id' => $this->warehouse_id,
                'transaction_type' => 'purchase',
                'notes' => $this->notes,
            ]);

            // Consolidate items by summing quantities for duplicates with the same item_id and expiration_date
            $transactionItemsData = collect($this->items)
                ->groupBy(function ($item) {
                    return $item['item_id'] . '-' . $item['expiration_date'];
                })
                ->map(function ($groupedItems) {
                    // Sum quantities for grouped items
                    return [
                        'item_id' => $groupedItems->first()['item_id'],
                        'quantity' => $groupedItems->sum('quantity'),
                        'expiration_date' => $groupedItems->first()['expiration_date']
                    ];
                });

            // Attach each consolidated item individually
            foreach ($transactionItemsData as $item) {
                $transaction->items()->attach($item['item_id'], [
                    'quantity' => $item['quantity'],
                    'expiration_date' => $item['expiration_date'],
                ]);
            }
        });


        $this->reset();
    }
}
