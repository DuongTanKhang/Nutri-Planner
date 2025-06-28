<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\reps\UserFavoriteFoodRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class FavoriteFoodController extends Controller
{
    protected $repo;

    public function __construct()
    {
        $this->repo = new UserFavoriteFoodRepository();
    }

    // [POST] /api/favorites/toggle
    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'food_id' => 'required|integer|exists:tbl_food,_id',
            ]);

            $userId = Auth::id();
            $foodId = $request->input('food_id');

            $action = $this->repo->toggleFavorite($userId, $foodId);

            return response()->json([
                'message' => $action === 'added' ? 'Added to favorites' : 'Removed from favorites',
                'status' => $action,
            ]);
        } catch (Exception $e) {
            Log::error('Toggle favorite error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to toggle favorite',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // [GET] /api/favorites
    public function index()
    {
        try {
            $userId = Auth::id();
            $foods = $this->repo->getFavoriteFoods($userId);

            return response()->json([
                'data' => $foods,
            ]);
        } catch (Exception $e) {
            Log::error('Get favorites error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get favorites',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // [GET] /api/favorites/ids
    public function getIds()
    {
        try {
            $userId = Auth::id();
            Log::info('User ID gọi getIds: ' . $userId);

            if (!$userId) {
                return response()->json(['ids' => []]); // fallback tránh lỗi
            }

            $ids = $this->repo->getFavoriteIds($userId);

            return response()->json([
                'ids' => $ids,
            ]);
        } catch (Exception $e) {
            Log::error('Get favorite IDs error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get favorite IDs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // [DELETE] /api/favorites/{food_id}
    public function remove($food_id)
    {
        try {
            $userId = Auth::id();
            $deleted = $this->repo->removeFavorite($userId, $food_id);

            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'Removed successfully' : 'Not found',
            ]);
        } catch (Exception $e) {
            Log::error('Remove favorite error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to remove favorite',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
