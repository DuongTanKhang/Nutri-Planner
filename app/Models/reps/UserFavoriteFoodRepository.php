<?php

namespace App\Models\reps;

use App\Models\UserFavoriteFood;
use Illuminate\Support\Facades\DB;

class UserFavoriteFoodRepository
{
    public function isFavorited($userId, $foodId): bool
    {
        return UserFavoriteFood::where('_user_id', $userId)
            ->where('_food_id', $foodId)
            ->exists();
    }

    public function toggleFavorite($userId, $foodId): string
    {
        $favorite = UserFavoriteFood::where('_user_id', $userId)
            ->where('_food_id', $foodId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return 'removed';
        }

        UserFavoriteFood::create([
            '_user_id'    => $userId,
            '_food_id'    => $foodId,
            '_created_at' => now(),
        ]);

        return 'added';
    }

    public function getFavoriteFoods($userId)
    {
        return UserFavoriteFood::with([
            'food.dietType',
            'food.cuisineType',
            'food.foodType',
        ])
            ->where('_user_id', $userId)
            ->get()
            ->pluck('food')
            ->filter();
    }


    public function getFavoriteIds($userId): array
    {
        return UserFavoriteFood::where('_user_id', $userId)
            ->pluck('_food_id')
            ->toArray();
    }

    public function removeFavorite($userId, $foodId): bool
    {
        return UserFavoriteFood::where('_user_id', $userId)
            ->where('_food_id', $foodId)
            ->delete() > 0;
    }
}
