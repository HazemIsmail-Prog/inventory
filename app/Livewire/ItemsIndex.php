<?php

namespace App\Livewire;

use App\Livewire\Forms\ItemForm;
use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ItemsIndex extends Component
{

    public ItemForm $form;

    #[Computed()]
    public function items()
    {
        return Item::query()
            ->withSum(['transactions as totalIncome' => function (Builder $q) {
                $q->whereIn('transactions.transaction_type', ['purchase', 'adjustment']);
            }], DB::raw('item_transaction.quantity'))
            ->withSum(['transactions as totalOutgoing' => function (Builder $q) {
                $q->whereIn('transactions.transaction_type', ['write_off']);
            }], DB::raw('item_transaction.quantity'))
            ->get();
    }

    public function save()
    {
        $this->form->updateOrCreate();
    }

    public function delete(Item $item)
    {
        $item->delete();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.items-index');
    }
}
