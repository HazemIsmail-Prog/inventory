<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function supplier() : BelongsTo {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse() : BelongsTo {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions() : HasMany {
        return $this->hasMany(Transaction::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'invoice_item')->withPivot('quantity', 'expiration_date','price_per_unit');
        ;
    }
}
