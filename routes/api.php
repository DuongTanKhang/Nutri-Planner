<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    CategoryController,
    FoodController,
    AllergenController,
    DietTypeController,
    GoalController,
    GoalDietRecommendationController,
    UserController,
    MealController,
    FavoriteFoodController
};

// Public APIs (Không cần login)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/foods-by-category', [FoodController::class, 'getFoodsGroupedByCategory']);
Route::get('/foods/{id}', [FoodController::class, 'getFoodDetail']);
Route::get('/allergens', [AllergenController::class, 'index']);
Route::get('/diet-types', [DietTypeController::class, 'index']);
Route::get('/goals', [GoalController::class, 'index']);
Route::get('/goal-diet-recommendations', [GoalDietRecommendationController::class, 'index']);
Route::get('/goal-diet-recommendations/filter', [GoalDietRecommendationController::class, 'filterByGoalId']);
Route::get('/meals-grouped-by-category', [MealController::class, 'groupByCategory']);
Route::get('/profile/{id}', [UserController::class, 'getProfile']);

// Auth APIs
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/refresh', [AuthController::class, 'refresh']);

// Protected APIs 
Route::middleware('auth:api')->group(function () {

    // 🔹 Authenticated User
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🔹 User Profile Steps
    Route::post('/users/update-step1', [UserController::class, 'updateBasicInfo']);
    Route::post('/users/update-step2', [UserController::class, 'updateGoalAndDiet']);
    Route::post('/users/update-step3', [UserController::class, 'updateAllergensUser']);
    Route::get('/user/allergens', [UserController::class, 'getUserAllergens']);

    // 🔹 Favorite Foods
    Route::post('/favorites/toggle', [FavoriteFoodController::class, 'toggle']);
    Route::get('/favorites', [FavoriteFoodController::class, 'index']);
    Route::get('/favorites/ids', [FavoriteFoodController::class, 'getIds']);
    Route::delete('/favorites/{food_id}', [FavoriteFoodController::class, 'remove']);
});
