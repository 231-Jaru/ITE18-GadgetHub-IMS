<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\Admins;

class AuthenticationController extends Controller
{
    /**
     * Register a new admin user.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'Username' => 'required|string|max:50|unique:admins,Username',
                'PasswordHash' => 'required|string|min:6',
                'Role' => 'nullable|string|in:Admin,Staff',
            ]);

            // Hash the password
            $validated['PasswordHash'] = Hash::make($validated['PasswordHash']);

            // Set default role if not provided
            if (!isset($validated['Role'])) {
                $validated['Role'] = 'Staff';
            }

            // Create the admin
            $admin = Admins::create($validated);

            // Automatically log in the newly registered admin
            $token = $admin->createToken('authToken')->plainTextToken;

            $userInfo = [
                'id'       => $admin->AdminID,
                'name'     => $admin->Username,
                'username' => $admin->Username,
                'role'     => $admin->Role,
                'type'     => 'admin',
            ];

            return response()->json([
                'response_code' => 201,
                'status'        => 'success',
                'message'       => 'Registration successful',
                'user_info'     => $userInfo,
                'token'         => $token,
                'token_type'    => 'Bearer',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login and return auth token.
     * Admin-only authentication (by Username).
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'    => 'required|string', // Username field (kept as 'email' for compatibility)
                'password' => 'required|string',
            ]);

            $username = $credentials['email'];
            $password = $credentials['password'];

            // Find Admin by Username
            $admin = Admins::where('Username', $username)->first();
                
            if (!$admin || !Hash::check($password, $admin->PasswordHash)) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Invalid credentials',
                ], 401);
            }

            // Create a token (using Sanctum if available, otherwise use session-based)
            $token = $admin->createToken('authToken')->plainTextToken;

                $userInfo = [
                'id'       => $admin->AdminID,
                'name'     => $admin->Username,
                'username' => $admin->Username,
                'role'     => $admin->Role,
                    'type'     => 'admin',
                ];

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'Login successful',
                'user_info'     => $userInfo,
                'token'         => $token,
                'token_type'    => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'response_code' => 422,
                'status'        => 'error',
                'message'       => 'Validation failed',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            Log::error('Login Error Stack: ' . $e->getTraceAsString());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Login failed: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Get current authenticated user info — protected route.
     */
    public function userInfo(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'response_code' => 401,
                    'status'        => 'error',
                    'message'       => 'Unauthorized',
                ], 401);
            }

            // Prepare admin user info
            if ($user instanceof Admins) {
                $userInfo = [
                    'id'       => $user->AdminID,
                    'name'     => $user->Username,
                    'username' => $user->Username,
                    'role'     => $user->Role,
                    'type'     => 'admin',
                ];
            } else {
                return response()->json([
                    'response_code' => 500,
                    'status'        => 'error',
                    'message'       => 'Unknown user type',
                ], 500);
            }

            return response()->json([
                'response_code' => 200,
                'status'        => 'success',
                'message'       => 'User info retrieved successfully',
                'user_info'     => $userInfo,
            ]);
        } catch (\Exception $e) {
            Log::error('User Info Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'Failed to fetch user info',
            ], 500);
        }
    }

    /**
     * Logout user and revoke tokens — protected route.
     */
    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();

                return response()->json([
                    'response_code' => 200,
                    'status'        => 'success',
                    'message'       => 'Successfully logged out',
                ]);
            }

            return response()->json([
                'response_code' => 401,
                'status'        => 'error',
                'message'       => 'User not authenticated',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return response()->json([
                'response_code' => 500,
                'status'        => 'error',
                'message'       => 'An error occurred during logout',
            ], 500);
        }
    }
}