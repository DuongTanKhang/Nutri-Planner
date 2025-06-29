<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\reps\UserRepository;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\UserAllergen;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    // Get user profile by ID
    public function getProfile($id)
    {
        $user = $this->userRepo->getUserDetails($id);
        if (!$user) return response()->json(['error' => 'User not found'], 404);

        $allergens = $this->userRepo->getUserAllergens($id);

        return response()->json([
            '_id' => $user->_id,
            '_username' => $user->_username,
            '_email' => $user->_email,
            '_full_name' => $user->_full_name,
            '_avatar' => $user->_avatar,
            '_dob' => $user->_dob,
            '_gender' => $user->_gender,
            '_weight_kg' => $user->_weight_kg,
            '_height_cm' => $user->_height_cm,
            '_activity_level' => $user->_activity_level,
            '_goal' => $user->_goal,
            'goal_name' => $user->goal_name,
            '_diet_type_id' => $user->_diet_type_id,
            'diet_type' => $user->diet_type,
            'allergens' => $allergens
        ]);
    }



    // Step 1: Update basic info (DOB, height, weight)

    public function updateBasicInfo(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            '_dob' => 'required|date',
            '_height_cm' => 'required|numeric|min:50|max:300',
            '_weight_kg' => 'required|numeric|min:10|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $success = $this->userRepo->updateProfile($user->_id, $request->only('_dob', '_height_cm', '_weight_kg'));

        if ($success) {
            $updatedUser = $this->userRepo->findById($user->_id);
            return response()->json([
                'message' => 'Basic info updated successfully.',
                'user' => $updatedUser
            ]);
        }

        return response()->json(['error' => 'Update failed.'], 500);
    }


    // Step 2: Update goal, diet type, and active level
    public function updateStep2(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            '_goal' => 'required|string',
            '_diet_type_id' => 'required|integer|exists:tbl_diet_type,_id',
            '_activity_level' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $success = DB::table('tbl_user')->where('_id', $user->_id)->update([
            '_goal' => $request->_goal,
            '_diet_type_id' => $request->_diet_type_id,
            '_activity_level' => $request->_activity_level,
            '_updated_at' => now()
        ]);

        if ($success) {
            $updatedUser = $this->userRepo->getUserDetails($user->_id);

            return response()->json([
                'message' => 'Step 2 completed.',
                'user' => [
                    '_id' => $updatedUser->_id,
                    '_username' => $updatedUser->_username,
                    '_email' => $updatedUser->_email,
                    '_full_name' => $updatedUser->_full_name,
                    '_avatar' => $updatedUser->_avatar,
                    '_dob' => $updatedUser->_dob,
                    '_gender' => $updatedUser->_gender,
                    '_weight_kg' => $updatedUser->_weight_kg,
                    '_height_cm' => $updatedUser->_height_cm,
                    '_activity_level' => $updatedUser->_activity_level,
                    '_goal' => $updatedUser->_goal,
                    'goal_name' => $updatedUser->goal_name,
                    '_diet_type_id' => $updatedUser->_diet_type_id,
                    'diet_type' => $updatedUser->diet_type,
                    'allergens' => $this->userRepo->getUserAllergens($user->_id)
                ]
            ]);
        }

        return response()->json(['error' => 'Failed to update Step 2'], 500);
    }



    // Step 3: Update allergens
    public function updateAllergensUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $allergenIds = $request->input('_allergen_ids', []);

        $success = $this->userRepo->updateAllergens($user->_id, $allergenIds);

        return $success
            ? response()->json(['message' => 'Allergens updated successfully.'])
            : response()->json(['error' => 'Failed to update allergens.'], 500);
    }

    public function getUserAllergens()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $allergens = UserAllergen::where('_user_id', $user->_id)
                ->with('allergen')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->_allergen_id,
                        'name' => $item->allergen->_name ?? null,
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'User allergens fetched successfully',
                'data' => $allergens
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user allergens: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch user allergens',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateProfile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validated = $request->validate([
            '_full_name' => 'nullable|string|max:255',
            '_dob' => 'nullable|date',
            '_weight_kg' => 'nullable|numeric|min:0',
            '_height_cm' => 'nullable|numeric|min:0',
            '_avatar' => 'nullable|file|image|max:2048',
        ]);


        if ($request->hasFile('_avatar')) {
            $file = $request->file('_avatar');
            $filename = 'avatar_' . $user->_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $validated['_avatar'] = 'storage/' . $path;
        }

        $success = $this->userRepo->updateProfile($user->_id, $validated);

        if (!$success) {
            return response()->json(['status' => false, 'message' => 'Failed to update profile'], 500);
        }

        $updatedUser = $this->userRepo->getUserDetails($user->_id);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $updatedUser
        ]);
    }
}
