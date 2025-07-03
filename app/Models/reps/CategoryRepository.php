<?php

namespace App\Models\reps;

use App\Models\Category;

class CategoryRepository
{
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function getAllActive()
    {
        return $this->model
            ->select('_id', '_name') 
            ->where('_active', 1)
            ->orderBy('_name')
            ->get();
    }


    public function find($id)
    {
        return $this->model->find($id);
    }
}
