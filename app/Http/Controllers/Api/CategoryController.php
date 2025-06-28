<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\reps\CategoryRepository;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryRepo->getAllActive();

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }
}