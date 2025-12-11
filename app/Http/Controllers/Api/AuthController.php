<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Import Str facade
use Illuminate\Support\Facades\Log;   // For logging

class AuthController extends Controller
{
    /**
     * Register new user
     */
   public function register(Request $request)
{
    // Validate request data
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    // If validation fails, return response with errors
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Create a new user with hashed password
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),  // Hash the password before saving
        'email_verified_at' => now(),  // Automatically verify email
    ]);

    // Generate API token for the user
    $token = $user->createToken('auth_token')->plainTextToken;

    // Log the registration event
    Log::info('New user registered: ' . $user->email);

    // Return success response with token and user info
    return response()->json([
        'message' => 'Registration successful',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
    ], 201);
}
    /**
     * Login user
     */
    public function login(Request $request)
    {
        // Validate login data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Attempt authentication
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            // Log failed login attempt
            Log::warning('Login failed for email: ' . $request->email);
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        // Get the authenticated user
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log successful login
        Log::info('User logged in: ' . $user->email);

        // Return success response with token and user info
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * Google Login
     */
    public function googleLogin(Request $request)
    {
        // Validate Google login data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find or create a user by Google email
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => Hash::make(Str::random(16)), // Random password for Google users
                'email_verified_at' => now(),
            ]
        );

        // Generate API token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log the Google login
        Log::info('Google login successful for email: ' . $request->email);

        // Return success response with token and user info
        return response()->json([
            'message' => 'Google login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        Log::info('User logged out: ' . $user->email);

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }
}
