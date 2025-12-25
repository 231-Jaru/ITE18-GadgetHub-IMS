<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated (admin or buyer)
        if (!session('api_token') && !session('user_id')) {
            // Store the intended URL for redirect after login
            if ($request->isMethod('GET')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }
            
            // For AJAX/API requests, return JSON response instead of redirect
            if ($request->expectsJson() || $request->ajax() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'error' => 'Please login to access this resource.'
                ], 401);
            }
            
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        // Only update last activity if it's been more than 5 minutes
        // This prevents constant session updates and back button issues
        $lastActivity = session('last_activity');
        if (!$lastActivity || now()->diffInMinutes($lastActivity) > 5) {
            session(['last_activity' => now()]);
        }

        return $next($request);
    }
}
