<?php

namespace App\Models\reps;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DietTypeRepository
{
    protected $table_name;
    protected $primaryKey;

    public function __construct()
    {
        $this->table_name = 'tbl_diet_type';
        $this->primaryKey = '_id';
    }

    public function getAll()
    {
        return DB::table($this->table_name)
            ->select('_id', '_name')
            ->where('_active', 1) 
            ->get();
    }
}
