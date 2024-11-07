<?php

namespace App\Livewire;

use App\Livewire\Forms\SupplierForm;
use App\Models\Supplier;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SuppliersIndex extends Component
{

    public SupplierForm $form;

    #[Computed()]
    public function suppliers()
    {
        return Supplier::query()
            ->get();
    }

    public function save()
    {
        $this->form->updateOrCreate();
    }

    public function delete(Supplier $supplier)
    {
        $supplier->delete();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.suppliers-index');
    }
}
