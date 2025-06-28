<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\diet_type;
use App\Models\allergen;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_user';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_username',
        '_email',
        '_password',
        '_full_name',
        '_avatar',
        '_gender',
        '_dob',
        '_weight_kg',
        '_height_cm',
        '_activity_level',
        '_goal',
        '_diet_type_id',
        '_status',
        '_created_at',
        '_updated_at',
    ];

    protected $hidden = [
        '_password',
    ];

    protected function casts(): array
    {
        return [
            '_dob' => 'date',
            '_created_at' => 'datetime',
            '_updated_at' => 'datetime',
            '_status' => 'boolean',
        ];
    }
    public function goal()
    {
        return $this->belongsTo(Goal::class, '_goal');
    }
    
    public function dietType()
    {
        return $this->belongsTo(diet_type::class, '_diet_type_id', '_id');
    }

    public function allergens()
    {
        return $this->belongsToMany(allergen::class, 'tbl_user_allergen', '_user_id', '_allergen_id');
    }

    public function getJWTIdentifier()
    {
        return $this->_id;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
