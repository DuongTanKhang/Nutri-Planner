<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MealController extends Controller
{
    public function groupByCategory(): JsonResponse
    {
        try {
            $categories = Category::with([
                'foods.meal',
                'foods.firstImage',
                'foods.dietType'
            ])
                ->where('_active', 1)
                ->get();

            $data = $categories->map(function ($category) {
                $mealsMap = [];

                foreach ($category->foods as $food) {
                    if (!$food->meal) continue;
                   
                    $meal = $food->meal;
                    $mealId = $meal->_id;

                    if (!isset($mealsMap[$mealId])) {
                        $mealsMap[$mealId] = [
                            'id' => $mealId,
                            'name' => $meal->_name,
                            'calories' => $meal->_calories ?? null,
                            'thumbnail' => $meal->_thumbnail ?? null,
                            'foods' => []
                        ];
                    }

                    $mealsMap[$mealId]['foods'][] = [
                        'id' => $food->_id,
                        'name' => $food->_name,
                        'image' => $food->firstImage?->_image,
                        'diet_type' => $food->dietType?->_name ?? ($food->_diet_type ?? 'Unknown')
                    ];
                }


                return [
                    'category' => $category->_name,
                    'meals' => array_values($mealsMap)
                ];
            })->filter(fn($c) => count($c['meals']) > 0)->values();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in groupByCategory(): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch meals by category.'
            ], 500);
        }
    }
}
