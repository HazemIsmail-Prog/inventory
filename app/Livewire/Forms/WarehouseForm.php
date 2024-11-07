<?php

namespace App\Livewire\Forms;

use App\Models\Warehouse;
use Livewire\Attributes\Validate;
use Livewire\Form;

class WarehouseForm extends Form
{
    public $id = null;
    #[Validate('required')]
    public string $name = '';

    public function updateOrCreate()
    {
        Warehouse::updateOrCreate(['id' => $this->id], $this->all());
        $this->reset();
    }
}
