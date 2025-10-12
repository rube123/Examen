<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'sakila';
    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;

    protected $fillable = [
        'store_id', 'first_name', 'last_name', 'email', 'address_id', 'active'
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'customer_id');
    }
}
