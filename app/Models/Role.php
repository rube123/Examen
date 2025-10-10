<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'display_name'];

    // Un rol tiene muchos usuarios
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
