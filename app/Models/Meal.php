<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $table = 'tbl_meal';
    protected $primaryKey = '_id';
    public $timestamps = true;

    protected $fillable = [
        '_name',
    ];

    public function foods()
    {
        return $this->hasMany(Food::class, '_meal_id', '_id');
    }

    
}
