<?php

namespace App\Models\reps;

use App\Models\goal;
use Illuminate\Support\Facades\DB;

class GoalRepository
{
    protected $table_name = 'tbl_goal';
    protected $primaryKey = '_id';

     public function getAll()
    {
        return DB::table($this->table_name)
            ->select('_id', '_name')
            ->where('_active', 1) 
            ->get();
    }

}
