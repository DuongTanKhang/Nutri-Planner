<?php

namespace App\Models\reps;

use Illuminate\Support\Facades\DB;

class GoalDietRecommendationRepository
{
    protected $table = 'tbl_goal_diet_recommendation';
    protected $primaryKey = '_id';

    public function getAll()
    {
        return DB::table($this->table)
            ->leftJoin('tbl_goal', 'tbl_goal_diet_recommendation._goal_id', '=', 'tbl_goal._id')
            ->leftJoin('tbl_diet_type', 'tbl_goal_diet_recommendation._diet_type_id', '=', 'tbl_diet_type._id')
            ->select(
                'tbl_goal_diet_recommendation.*',
                'tbl_goal._name as goal_name',
                'tbl_diet_type._name as diet_type_name'
            )
            ->get();
    }

    public function getDietTypesByGoalId($goalId)
    {
        return DB::table($this->table)
            ->join('tbl_diet_type', $this->table . '._diet_type_id', '=', 'tbl_diet_type._id')
            ->where($this->table . '._goal_id', $goalId)
            ->select('tbl_diet_type._id', 'tbl_diet_type._name', 'tbl_diet_type._description')
            ->get();
    }
}
