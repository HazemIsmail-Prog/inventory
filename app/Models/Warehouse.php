<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_warehouse_id');
    }

}
