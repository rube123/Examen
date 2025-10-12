<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $connection = 'sakila';
    protected $table = 'store';
    protected $primaryKey = 'store_id';
    public $timestamps = false;
}
