<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $connection = 'sakila';
    protected $table      = 'inventory';
    protected $primaryKey = 'inventory_id';
    public    $timestamps = false;

    protected $fillable = ['film_id','store_id'];

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'film_id', 'film_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'inventory_id', 'inventory_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(InventoryHistory::class, 'inventory_id', 'inventory_id');
    }
}
