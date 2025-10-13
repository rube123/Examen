<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $connection = 'sakila';
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    public $timestamps = false;

    public function film()
    {
        return $this->belongsTo(Film::class, 'film_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'inventory_id');
    }
    public function history()
{
    return $this->hasMany(InventoryHistory::class, 'inventory_id', 'inventory_id');
}
}
