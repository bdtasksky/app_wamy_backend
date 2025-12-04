<?php

namespace App\Http\Controllers;

use App\Models\WebUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    // Register new WebUser
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email'=> 'required|email|unique:web_users,email',
            'mobile' => 'required|string|unique:web_users,mobile',
            'is_notify' => 'required|boolean',
            'password' => 'required|confirmed|min:6',
            'social_id' => 'nullable|string',
            'avatar' => 'nullable|string',
            'bg_image' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = WebUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'is_notify' => $request->is_notify,
            'password' => Hash::make($request->password),
            'social_id' => $request->social_id,
            'avatar' => $request->avatar,
            'bg_image' => $request->bg_image,
            'status' => $request->filled('status') ? $request->status : 1
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile
            ],
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], 201);

    }

    // Login for WebUser
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = trim($request->email);
        $password = $request->password;

        // find user using the WebUser model
        $user = WebUser::where('email', $email)->first();

        if (! $user) {
            Log::warning('Login failed - user not found', ['email' => $email]);
            return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
        }

        if (! Hash::check($password, $user->password)) {
            Log::warning('Login failed - wrong password', ['email' => $email]);
            return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
        }

        // Create token using the api guard (jwt)
        // This requires your 'api' guard to use driver 'jwt' (see config/auth.php)
        try {
            $token = auth('api')->login($user); // returns JWT token string
        } catch (\Throwable $e) {
            Log::error('JWT login error', ['err' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Could not create token'], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ]);
    }

    // Get authenticated user
    public function me()
    {
        $user = auth('api')->user();
        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }

    // Logout (invalidate token)
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            try {
                JWTAuth::invalidate($token);
            } catch (\Exception $e) {
                // ignore or log
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
