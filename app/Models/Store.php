<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Store extends Model
{
    // Usa esta línea solo si definiste una conexión 'sakila' en config/database.php.
    protected $connection = 'sakila';

    protected $table = 'store';
    protected $primaryKey = 'store_id';
    public $timestamps = false;

    protected $fillable = [
        'manager_staff_id',
        'address_id',
    ];

    protected $casts = [
        'last_update' => 'datetime',
    ];

    // staff.staff_id <- store.manager_staff_id
    public function gerente(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'manager_staff_id', 'staff_id');
    }

    // address.address_id <- store.address_id
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    // staff.store_id -> store.store_id
    public function empleados(): HasMany
    {
        return $this->hasMany(Staff::class, 'store_id', 'store_id');
    }

    // customer.store_id -> store.store_id
    public function clientes(): HasMany
    {
        return $this->hasMany(Customer::class, 'store_id', 'store_id');
    }

    // inventory.store_id -> store.store_id
    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventory::class, 'store_id', 'store_id');
    }

    // store -> inventory -> rental
    public function rentas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Rental::class,    // final
            Inventory::class, // intermedio
            'store_id',       // FK en inventory hacia store
            'inventory_id',   // FK en rental hacia inventory
            'store_id',       // PK local en store
            'inventory_id'    // PK en inventory usada por rental
        );
    }
}
