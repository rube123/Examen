<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;

    protected $fillable = [
        'store_id',
        'first_name',
        'last_name',
        'email',
        'address_id',
        'active',
        'create_date',
        'last_update',
        'curp', // si la agregas como campo nuevo
        'phone',
    ];

    // Un cliente tiene muchas rentas
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id');
    }
}

