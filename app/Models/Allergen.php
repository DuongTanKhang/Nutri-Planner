<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    protected $table = 'tbl_allergen';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_name',
        '_description',
        '_active',
    ];
}
