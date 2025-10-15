<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    protected $connection = 'sakila';
    protected $table      = 'staff';
    protected $primaryKey = 'staff_id';
    public    $timestamps = false;

    protected $fillable = [
        'first_name','last_name','address_id','email',
        'store_id','username','password','active',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }
}
