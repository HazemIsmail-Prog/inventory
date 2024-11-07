<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_item')->withPivot('quantity', 'expiration_date'); // Include pivot fields if necessary
        ;
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'item_transaction')->withPivot('quantity', 'expiration_date'); // Include pivot fields
        ;
    }
}
