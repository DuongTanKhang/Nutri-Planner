<?php

namespace App\Models\reps;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Support\Facades\Log;
use App\Models\UserAllergen;

class FoodRepository
{
    /**
     * Get all foods grouped by category, with full food data and first image.
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function getFoodsGroupedByCategory()
    {
        try {
            $categories = Category::with([
                'foods' => function ($q) {
                    $q->select('_id', '_name', '_meal_id', '_food_type_id', '_cuisine_type_id', '_category_id', '_diet_type_id');
                },
                'foods.firstImage' => function ($q) {
                    $q->select('_id', '_food_id', '_image');
                },
                'foods.meal' => function ($q) {
                    $q->select('_id', '_name');
                },
                'foods.category' => function ($q) {
                    $q->select('_id', '_name');
                },
                'foods.cuisineType' => function ($q) {
                    $q->select('_id', '_name');
                },
                'foods.dietType' => function ($q) {
                    $q->select('_id', '_name');
                },
                'foods.foodType' => function ($q) {
                    $q->select('_id', '_name');
                },
                'foods.foodIngredients.ingredient' => function ($q) {
                    $q->select('_id', '_name', '_allergen_id');
                },
                'foods.foodIngredients.ingredient.allergen' => function ($q) {
                    $q->select('_id', '_name');
                }
            ])
                ->select('_id', '_name')
                ->get();

            // mapping logic giữ nguyên...
            return $categories->map(function ($category) {
                return [
                    '_id' => $category->_id,
                    '_name' => $category->_name,
                    'foods' => $category->foods->map(function ($food) {
                        $allergenList = [];
                        foreach ($food->foodIngredients as $fi) {
                            if ($fi->ingredient && $fi->ingredient->allergen) {
                                $a = $fi->ingredient->allergen;
                                $allergenList[$a->_id] = [
                                    '_id' => $a->_id,
                                    '_name' => $a->_name,
                                ];
                            }
                        }

                        return [
                            '_id' => $food->_id,
                            '_name' => $food->_name,
                            '_meal_id' => $food->_meal_id,
                            '_food_type_id' => $food->_food_type_id,
                            '_cuisine_type_id' => $food->_cuisine_type_id,
                            '_category_id' => $food->_category_id,
                            '_diet_type_id' => $food->_diet_type_id,
                            'image_url' => $food->firstImage?->_image,
                            'meal' => $food->meal?->_name,
                            'category' => $food->category?->_name,
                            'cuisine_type' => $food->cuisineType?->_name,
                            'diet_type' => $food->dietType?->_name,
                            'food_type' => $food->foodType?->_name,
                            'calories' => $this->calculateCaloriesFromIngredients($food),
                            'allergens' => array_values($allergenList),
                        ];
                    })
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error fetching foods grouped by category: ' . $e->getMessage());
            return null;
        }
    }



    /**
     * Get detailed food info including ingredients and preparation.
     *
     * @param int $id
     * @return array|null
     */
    public function getFoodDetail(int $id, ?int $userId = null): ?array
    {
        $food = Food::with([
            'foodIngredients.ingredient.allergen',
            'foodIngredients.preparationMethod',
            'firstImage'
        ])->find($id);

        if (!$food) return null;

        $userAllergenIds = [];

        if ($userId) {
            $userAllergenIds = DB::table('tbl_user_allergen')
                ->where('_user_id', $userId)
                ->pluck('_allergen_id')
                ->toArray();
        }

        $ingredients = $food->foodIngredients->map(function ($fi) use ($userAllergenIds) {
            $ingredient = $fi->ingredient;

            return [
                'name' => $ingredient->_name ?? null,
                'amount' => $fi->_amount,
                'note' => $fi->_note,
                'preparation' => $fi->preparationMethod?->_prepar,
                'allergen' => $ingredient->allergen->_name ?? null,
                'is_allergen' => $ingredient->_allergen_id && in_array($ingredient->_allergen_id, $userAllergenIds),
            ];
        });

        $nutrients = DB::table('tbl_food_ingredient as fi')
            ->join('tbl_ingredient_nutrient as ingr_nut', 'fi._ingredient_id', '=', 'ingr_nut._ingredient_id')
            ->join('tbl_nutrient as n', 'ingr_nut._nutrient_id', '=', 'n._id')
            ->leftJoin('tbl_nutrition_group as g', 'n._group_id', '=', 'g._id')
            ->select(
                'n._id as id',
                'n._name as name',
                'n._unit as unit',
                'g._name as group',
                DB::raw('SUM(fi._amount) as total_ingredient_amount'),
                DB::raw('ROUND(AVG(ingr_nut._value_per_100g), 2) as avg_value_per_100g'),
                DB::raw('ROUND(SUM((fi._amount / 100.0) * ingr_nut._value_per_100g), 2) as value')
            )
            ->where('fi._food_id', $id)
            ->groupBy('n._id', 'n._name', 'n._unit', 'g._name')
            ->orderBy('g._name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'unit' => $item->unit,
                    'group' => $item->group,
                    'total_ingredient_amount' => (float) $item->total_ingredient_amount,
                    'avg_value_per_100g' => (float) $item->avg_value_per_100g,
                    'value' => (float) $item->value,
                ];
            })
            ->toArray();



        return [
            'id' => $food->_id,
            'name' => $food->_name,
            'image' => $food->firstImage?->_image,
            'ingredients' => $ingredients,
            'nutrients' => $nutrients,
        ];
    }
    private function calculateCaloriesFromIngredients($food): float
    {
        $ingredients = $food->foodIngredients;

        $totalCalories = 0;

        foreach ($ingredients as $fi) {
            $ingredient = $fi->ingredient;

            if ($ingredient) {
                $caloriesPer100g = DB::table('tbl_ingredient_nutrient as in')
                    ->join('tbl_nutrient as n', 'in._nutrient_id', '=', 'n._id')
                    ->where('in._ingredient_id', $ingredient->_id)
                    ->where('n._name', 'Calories')
                    ->value('in._value_per_100g');
                $amountInG = $fi->_amount;

                if (!is_null($caloriesPer100g) && !is_null($amountInG)) {
                    $totalCalories += ($amountInG / 100) * $caloriesPer100g;
                }
            }
        }
        return round($totalCalories, 2);
    }
}
