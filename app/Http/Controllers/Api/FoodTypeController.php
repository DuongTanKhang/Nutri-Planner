<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodType;

class FoodTypeController extends Controller
{
    public function index()
    {
        $types = FoodType::where('_active', true)->get();

        return response()->json([
            'status' => true,
            'message' => 'List of food types',
            'data' => $types,
        ]);
    }
}
