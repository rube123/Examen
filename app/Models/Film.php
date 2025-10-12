<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $connection = 'sakila';
    protected $table = 'film';
    protected $primaryKey = 'film_id';
    public $timestamps = false;

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'film_id');
    }
}
