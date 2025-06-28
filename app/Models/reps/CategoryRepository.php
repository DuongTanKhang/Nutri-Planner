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
            ->where(function ($query) {
                $query->where('_active', 1);
            })
            ->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

}

?>