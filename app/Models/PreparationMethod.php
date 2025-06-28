<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreparationMethod extends Model
{
    protected $table = 'tbl_preparation_method';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_prepar',
        '_food_ingredient_id',
    ];

    public function foodIngredient()
    {
        return $this->belongsTo(FoodIngredient::class, '_food_ingredient_id', '_id');
    }
}
