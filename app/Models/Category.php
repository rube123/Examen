<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'sakila';
    protected $table      = 'category';
    protected $primaryKey = 'category_id';
    public    $timestamps = false;

    protected $fillable = ['name'];
}
