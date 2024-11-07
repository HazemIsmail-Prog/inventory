<?php

namespace App\Livewire;

use App\Livewire\Forms\InvoiceForm;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Warehouse;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class InvoicesIndex extends Component
{

    public InvoiceForm $form;

    #[Computed()]
    public function items()
    {
        return Item::all();
    }

    #[Computed()]
    public function warehouses()
    {
        return Warehouse::all();
    }

    #[Computed()]
    public function suppliers()
    {
        $suppliers = Supplier::query()
            ->get();
        return $suppliers;
    }
    #[Computed()]
    public function invoices()
    {
        $invoices = Invoice::query()
            ->with('items')
            ->with('supplier')
            ->with('warehouse')
            ->get();
        return $invoices;
    }

    public function save() {
        $this->form->save();
    }

    public function delete(Invoice $invoice) {
        $invoice->delete();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.invoices-index');
    }
}
