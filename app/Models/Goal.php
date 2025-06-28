<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class goal extends Model
{
    protected $table = 'tbl_goal';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_name',
        '_description',
        '_active'
    ];
}
