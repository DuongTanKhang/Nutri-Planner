<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietType extends Model
{
    protected $table = 'tbl_diet_type';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_name',
        '_description',
        '_active',
    ];

    public function meals()
    {
        return $this->hasMany(Meal::class, '_diet_type_id', '_id');
    }
}
