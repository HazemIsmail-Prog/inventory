<?php

namespace App\Livewire\Forms;

use App\Models\Item;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ItemForm extends Form
{
    public $id = null;
    public string $name = '';
    public string $unit = '';

    public function updateOrCreate()
    {
        Item::updateOrCreate(['id' => $this->id], $this->all());
        $this->reset();
    }
}
