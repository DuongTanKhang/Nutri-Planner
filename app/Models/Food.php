<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'tbl_food';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_name',
        '_meal_id',
        '_food_type_id',
        '_category_id',
        '_cuisine_type_id',
        '_diet_type_id',
    ];
    protected $appends = ['first_image_url'];

    public function meal()
    {
        return $this->belongsTo(Meal::class, '_meal_id', '_id');
    }

    public function images()
    {
        return $this->hasMany(FoodImage::class, '_food_id', '_id');
    }

    public function firstImage()
    {
        return $this->hasOne(FoodImage::class, '_food_id', '_id')->orderBy('_id');
    }

    public function getFirstImageUrlAttribute()
    {
        return $this->firstImage?->_image ?? null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, '_category_id', '_id');
    }

    public function cuisineType()
    {
        return $this->belongsTo(CuisineType::class, '_cuisine_type_id', '_id');
    }

    public function foodType()
    {
        return $this->belongsTo(FoodType::class, '_food_type_id', '_id');
    }

    public function dietType()
    {
        return $this->belongsTo(DietType::class, '_diet_type_id', '_id');
    }

    public function getCategoryNameAttribute()
    {
        return $this->category?->_name ?? null;
    }

    public function getCuisineTypeNameAttribute()
    {
        return $this->cuisineType?->_name ?? null;
    }

    public function getFoodTypeNameAttribute()
    {
        return $this->foodType?->_name ?? null;
    }

    public function getDietTypeNameAttribute()
    {
        return $this->dietType?->_name ?? null;
    }
    public function foodIngredients()
    {
        return $this->hasMany(FoodIngredient::class, '_food_id', '_id');
    }
    public function nutrients()
    {
        return $this->hasMany(FoodNutrient::class, '_food_id');
    }
}
