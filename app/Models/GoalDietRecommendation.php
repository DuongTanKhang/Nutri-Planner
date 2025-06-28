<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalDietRecommendation extends Model
{
    protected $table = 'tbl_goal_diet_recommendation';
    protected $primaryKey = '_id';
    public $timestamps = false;

    protected $fillable = [
        '_goal_id',
        '_diet_type_id',
        '_note',
        '_active',
        '_priority'
    ];

    public function goal()
    {
        return $this->belongsTo(goal::class, '_goal_id');
    }

    public function dietType()
    {
        return $this->belongsTo(diet_type::class, '_diet_type_id');
    }
}