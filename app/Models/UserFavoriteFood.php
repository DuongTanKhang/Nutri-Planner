<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoriteFood extends Model
{
    protected $table = 'tbl_user_favorite_food';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = ['_user_id', '_food_id', '_created_at'];

    public function user()
    {
        return $this->belongsTo(User::class, '_user_id', '_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, '_food_id', '_id');
    }
}
