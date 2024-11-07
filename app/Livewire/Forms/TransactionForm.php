<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TransactionForm extends Form
{
    public $id;
    public string|null $transaction_type;
    public string|null $notes;
    public int|null $source_warehouse_id;
    public int|null $destination_warehouse_id;
    public array $items = [];

    public function save()
    {
        $transaction = Auth::user()->transactions()->create($this->except('id', 'items'));

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

        $this->reset();
    }
}
