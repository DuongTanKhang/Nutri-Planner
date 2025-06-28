<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodNutrient extends Model
{
    protected $table = 'tbl_food_nutrient';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = ['_food_id', '_nutrient_id', '_amount', '_value_per_100g'];

    public function food()
    {
        return $this->belongsTo(Food::class, '_food_id');
    }

    public function nutrient()
    {
        return $this->belongsTo(Nutrient::class, '_nutrient_id');
    }
}
