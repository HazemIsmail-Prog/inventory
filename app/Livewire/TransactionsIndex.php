<?php

namespace App\Livewire;

use App\Livewire\Forms\TransactionForm;
use App\Models\Transaction;
use App\Models\Warehouse;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class TransactionsIndex extends Component
{

    public TransactionForm $form;

    #[Computed()]
    public function warehouses()
    {
        return Warehouse::all();
    }

    #[Computed()]
    public function transactions()
    {
        $transactions = Transaction::query()
            ->with('sourceWarehouse', 'destinationWarehouse', 'invoice.supplier','items')
            ->get();
        return $transactions;
    }

    public function save()
    {
        $this->form->save();
    }

    public function deleteTransaction(Transaction $transaction)
    {
        $transaction->delete();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.transactions-index');
    }
}
