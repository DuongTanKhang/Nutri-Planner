<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAllergen extends Model
{
    protected $table = 'tbl_user_allergen';
    public $timestamps = false;

    protected $fillable = ['_user_id', '_allergen_id'];

    public function allergen()
    {
        return $this->belongsTo(Allergen::class, '_allergen_id', '_id');
    }
}
