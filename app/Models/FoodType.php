<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodType extends Model
{
    protected $table = 'tbl_food_type';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_name',
        '_introduction',
        '_active',
    ];

    public function meals()
    {
        return $this->hasMany(Meal::class, '_food_type_id', '_id');
    }
}
