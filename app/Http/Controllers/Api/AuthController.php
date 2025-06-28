<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\reps\UserRepository;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $userRep;

    public function __construct()
    {
        $this->userRep = new UserRepository();
    }

    // Login API (POST /api/login)
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '_email' => 'required|email',
            '_password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/'
            ],
        ], [
            '_password.regex' => 'Password must contain at least one uppercase letter and one special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $loginUser = $this->userRep->attemptLogin($request->_email, $request->_password);

        if (!$loginUser) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        
        $user = \App\Models\User::with('allergens')->find($loginUser->_id);
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ], 200);
    }


    //Get current user from token (GET /api/user)
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json([
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    // Logout (POST /api/logout)
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to logout',
            ], 400);
        }
    }

    // Register API (POST /api/register)
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '_name' => 'required|string|max:100',
            '_username' => 'required|string|max:50|unique:tbl_user,_username',
            '_email' => 'required|email|unique:tbl_user,_email',
            '_password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/'
            ],
            '_confirm_password' => 'required|same:_password',
            '_gender' => 'required|numeric|in:1,2,3',
        ], [
            '_password.regex' => 'Password must contain at least one uppercase letter and one special character.',
            '_confirm_password.same' => 'Passwords do not match.',
        ])->setAttributeNames([
            '_name' => 'fullName',
            '_username' => 'username',
            '_email' => 'email',
            '_password' => 'password',
            '_confirm_password' => 'confirmPassword',
            '_gender' => 'gender'
        ]);



        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = $this->userRep->register($request->only([
            '_name',
            '_username',
            '_email',
            '_password',
            '_gender'
        ]));

        if (!$user_id) {
            return response()->json(['message' => 'Registration failed'], 500);
        }

        $user = \App\Models\User::find($user_id);
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user
        ], 201);
    }
    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();

            return response()->json([
                'token' => $newToken,
                'message' => 'Token refreshed successfully'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 401);
        }
    }
}
