<?php

namespace App\Livewire\Forms;

use App\Models\Supplier;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SupplierForm extends Form
{
    public $id = null;
    public string $name = '';
    public string $phone = '';

    public function updateOrCreate()
    {
        Supplier::updateOrCreate(['id' => $this->id], $this->all());
        $this->reset();
    }
}
