<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $connection = 'sakila';
    protected $table      = 'customer';
    protected $primaryKey = 'customer_id';
    public    $timestamps = false;

    protected $fillable = [
        'store_id','first_name','last_name','email','address_id','active',
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }
}
