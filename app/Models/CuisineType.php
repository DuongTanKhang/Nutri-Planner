<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuisineType extends Model
{
    protected $table = 'tbl_cuisine_type';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_name',
        '_description',
        '_active',
    ];

    public function meals()
    {
        return $this->hasMany(Meal::class, '_cuisine_type_id', '_id');
    }
}
