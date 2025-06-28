<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'tbl_category';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = ['_name', '_active'];

    public function foods()
    {
        return $this->hasMany(\App\Models\Food::class, '_category_id', '_id');
    }
}
