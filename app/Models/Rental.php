<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $connection = 'sakila';
    protected $table = 'rental';
    protected $primaryKey = 'rental_id';
    public $timestamps = false;

    protected $fillable = ['rental_date', 'inventory_id', 'customer_id', 'staff_id', 'return_date'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'rental_id');
    }
}
