<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionGroup extends Model
{
    protected $table = 'tbl_nutrition_group';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = ['_name', '_active'];

    public function nutrients()
    {
        return $this->hasMany(Nutrient::class, '_group_id');
    }
}
