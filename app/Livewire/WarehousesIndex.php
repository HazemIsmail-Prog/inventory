<?php

namespace App\Livewire;

use App\Helpers\GetData;
use App\Livewire\Forms\WarehouseForm;
use App\Models\Warehouse;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class WarehousesIndex extends Component
{

    public WarehouseForm $form;

    #[Computed()]
    public function warehouses()
    {
        return Warehouse::query()
            ->get()->map(function ($warehouse) {
                $warehouse->available_items = GetData::getAvailableItems($warehouse)['data'] ?? [];
                return $warehouse;
            });
    }

    public function save() {
        $this->form->updateOrCreate();
    }

    public function delete(Warehouse $warehouse)
    {
        $warehouse->delete();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.warehouses-index');
    }
}
