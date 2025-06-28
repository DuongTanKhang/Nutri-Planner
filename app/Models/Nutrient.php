<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    protected $table = 'tbl_nutrient';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = ['_name', '_unit', '_group_id'];

    public function group()
    {
        return $this->belongsTo(NutritionGroup::class, '_group_id');
    }

    public function foodNutrients()
    {
        return $this->hasMany(FoodNutrient::class, '_nutrient_id');
    }
}
