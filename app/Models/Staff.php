<?php

namespace App\Models\Sakila;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $connection = 'sakila';
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    public $timestamps = false;

    protected $fillable = [
        'first_name', 'last_name', 'address_id', 'email',
        'store_id', 'username', 'password'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}


