<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $table = 'tbl_ingredient';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = ['_name', '_allergen_id'];

    public function allergen()
    {
        return $this->belongsTo(Allergen::class, '_allergen_id', '_id');
    }

    public function foodIngredients()
    {
        return $this->hasMany(FoodIngredient::class, '_ingredient_id', '_id');
    }
}
