<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\reps\FoodRepository;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class FoodController extends Controller
{
    protected $foodRep;

    public function __construct(FoodRepository $foodRep)
    {
        $this->foodRep = $foodRep;
    }

    public function getFoodsGroupedByCategory(): JsonResponse
    {
        try {
            $data = $this->foodRep->getFoodsGroupedByCategory();

            return response()->json([
                'status' => true,
                'message' => 'List of foods grouped by category',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching foods: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFoodDetail($id)
    {
        try {
            $userId = null;

            try {
                if (JWTAuth::getToken()) {
                    $user = JWTAuth::parseToken()->authenticate();
                    $userId = $user->_id;
                }
            } catch (\Exception $e) {
            }

            $food = $this->foodRep->getFoodDetail($id, $userId);

            if (!$food) {
                return response()->json(['status' => false, 'message' => 'Food not found or error occurred'], 404);
            }

            return response()->json(['status' => true, 'message' => 'Food detail fetched successfully', 'data' => $food]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }
}
