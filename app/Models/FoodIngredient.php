<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodIngredient extends Model
{
    protected $table = 'tbl_food_ingredient';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_food_id',
        '_ingredient_id',
        '_amount',
        '_note',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, '_ingredient_id', '_id');
    }

    public function preparationMethod()
    {
        return $this->hasOne(PreparationMethod::class, '_food_ingredient_id', '_id');
    }
}
