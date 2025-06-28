<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\reps\GoalDietRecommendationRepository;
use Illuminate\Http\JsonResponse;

class GoalDietRecommendationController extends Controller
{
    protected $goalDietRep;

    public function __construct(GoalDietRecommendationRepository $goalDietRep)
    {
        $this->goalDietRep = $goalDietRep;
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->goalDietRep->getAll();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch goal diet recommendations.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function filterByGoalId(): JsonResponse
    {
        $goalId = request()->query('goal_id');

        if (!$goalId) {
            return response()->json([
                'success' => false,
                'message' => 'goal_id parameter is required.'
            ], 400);
        }

        try {
            $data = $this->goalDietRep->getDietTypesByGoalId($goalId);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch diet types for the given goal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
