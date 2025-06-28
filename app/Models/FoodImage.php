<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodImage extends Model
{
    protected $table = 'tbl_food_image';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_image',
        '_food_id',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class, '_food_id', '_id');
    }
}
