<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\AuthenticationController as APIAuthController;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // If user is already logged in, redirect to dashboard
        if (session('api_token')) {
            return redirect('/dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        // Registration is disabled for inventory management system
        return redirect('/login')->with('error', 'Registration is disabled. This is an admin-only inventory management system.');
    }

    /**
     * Handle login via API
     */
    public function login(Request $request)
    {
        // Rate limiting check with time-based reset (10 attempts, resets after 5 minutes)
        $key = 'login_attempts_' . $request->ip();
        $attemptsKey = $key . '_count';
        $attemptsTimeKey = $key . '_time';
        
        $attempts = session($attemptsKey, 0);
        $lastAttemptTime = session($attemptsTimeKey, 0);
        
        // Reset attempts if 5 minutes have passed
        if ($lastAttemptTime && now()->diffInMinutes($lastAttemptTime) > 5) {
            $attempts = 0;
            session()->forget($attemptsKey);
            session()->forget($attemptsTimeKey);
        }
        
        if ($attempts >= 10) {
            $minutesLeft = max(1, 5 - now()->diffInMinutes($lastAttemptTime));
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$minutesLeft} minutes.",
            ])->onlyInput('email');
        }

        $credentials = $request->validate([
            'email'    => 'required|string', // Can be username or email
            'password' => 'required|string',
        ], [
            'email.required' => 'Username or email is required.',
            'password.required' => 'Password is required.',
        ]);

        try {
            // Merge credentials into the original request for the API controller
            // The API controller will validate these fields
            $request->merge([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ]);
            
            // Ensure request method is POST
            $request->setMethod('POST');
            
            // Call API AuthenticationController - this is the SINGLE SOURCE OF TRUTH for authentication
            // Pass the original request so session is preserved
            $apiAuth = new APIAuthController();
            $apiResponse = $apiAuth->login($request);
            $responseData = json_decode($apiResponse->getContent(), true);
            $statusCode = $apiResponse->getStatusCode();
            

            if ($statusCode === 200 && isset($responseData['token'])) {
                // Clear login attempts FIRST before regenerating session
                session()->forget($attemptsKey);
                session()->forget($attemptsTimeKey);
                
                // Regenerate session ID for security
                $request->session()->regenerate();
                
                // Store API token and user info in session
                $userInfo = $responseData['user_info'] ?? [];
                session([
                    'api_token'   => $responseData['token'],
                    'user_id'     => $userInfo['id'] ?? null,
                    'user_name'   => $userInfo['name'] ?? null,
                    'user_email'  => $userInfo['email'] ?? null,
                    'user_type'   => $userInfo['type'] ?? null, // 'admin' or 'buyer'
                    'user_role'   => $userInfo['role'] ?? null, // For admins
                    'login_time'  => now(),
                    'last_activity' => now()
                ]);
                
                // Redirect to dashboard (admin-only)
                $intended = $request->session()->pull('url.intended', '/dashboard');
                return redirect($intended)->with('success', 'Welcome back, ' . ($userInfo['name'] ?? 'User') . '!');
            } else {
                // Increment login attempts only on actual authentication failure
                if ($statusCode === 401 || $statusCode === 422) {
                    session([
                        $attemptsKey => $attempts + 1,
                        $attemptsTimeKey => now()
                    ]);
                }
                
                $errorMessage = $responseData['message'] ?? 'Invalid credentials. Please try again.';
                $apiErrors = $responseData['errors'] ?? [];
                
                // Convert API errors to Laravel validation errors format
                $errors = ['email' => $errorMessage];
                if (!empty($apiErrors)) {
                    foreach ($apiErrors as $field => $messages) {
                        if (is_array($messages)) {
                            $errors[$field] = $messages[0] ?? $errorMessage;
                        } else {
                            $errors[$field] = $messages;
                        }
                    }
                }
                
                return back()->withErrors($errors)->withInput($request->only('email'));
            }
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            
            // Increment login attempts on exception
            session([
                $attemptsKey => $attempts + 1,
                $attemptsTimeKey => now()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again later.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Handle registration via API
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|min:4|max:100',
            'email'    => 'required|string|email|max:100',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Name is required.',
            'name.min' => 'Name must be at least 4 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        try {
            // Ensure request method is POST
            $request->setMethod('POST');
            
            // Call API AuthenticationController directly
            $apiAuth = new APIAuthController();
            $apiResponse = $apiAuth->register($request);
            $responseData = json_decode($apiResponse->getContent(), true);
            $statusCode = $apiResponse->getStatusCode();

            if ($statusCode === 201) {
                return redirect('/login')->with('success', 'Registration successful! Please login with your credentials.');
            } else {
                $apiErrors = $responseData['errors'] ?? [];
                $errorMessage = $responseData['message'] ?? 'Registration failed. Please try again.';
                
                // Convert API errors to Laravel validation errors format
                $errors = [];
                if (!empty($apiErrors)) {
                    foreach ($apiErrors as $field => $messages) {
                        if (is_array($messages)) {
                            $errors[$field] = $messages[0] ?? $errorMessage;
                        } else {
                            $errors[$field] = $messages;
                        }
                    }
                } else {
                    // If no specific field errors, show general message
                    $errors['email'] = $errorMessage;
                }
                
                return back()->withErrors($errors)->withInput($request->except('password', 'password_confirmation'));
            }
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'An error occurred during registration. Please try again later.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Handle logout via API
     */
    public function logout(Request $request)
    {
        try {
            $token = session('api_token');
            $userName = session('user_name', 'User');
            
            // If we have a token, revoke it from the database
            if ($token) {
                try {
                    // Extract token ID from the token string (format: "id|token")
                    $tokenParts = explode('|', $token);
                    if (count($tokenParts) === 2) {
                        $tokenId = $tokenParts[0];
                        // Delete the token from personal_access_tokens table
                        DB::table('personal_access_tokens')
                            ->where('id', $tokenId)
                            ->delete();
                    }
                } catch (\Exception $e) {
                    Log::error('Logout API Error: ' . $e->getMessage());
                    // Continue with logout even if token deletion fails
                }
            }
            
            // Clear all session data first
            session()->flush();
            
            // Regenerate session ID for security (after flush to avoid issues)
            $request->session()->regenerate();
            
            return redirect('/login')->with('success', 'Goodbye, ' . $userName . '! You have been logged out successfully.');
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            // Even if there's an error, try to clear session and redirect
            session()->flush();
            return redirect('/login')->with('success', 'You have been logged out.');
        }
    }
}
