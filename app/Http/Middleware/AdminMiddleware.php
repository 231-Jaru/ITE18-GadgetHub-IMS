<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // First check if user is authenticated
        if (!session('api_token') && !session('user_id')) {
            if ($request->isMethod('GET')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        // Check if user is admin
        $userType = session('user_type');
        if ($userType !== 'admin') {
            return redirect('/home')->with('error', 'Access denied. Admin privileges required.');
        }
        
        // Only update last activity if it's been more than 5 minutes
        $lastActivity = session('last_activity');
        if (!$lastActivity || now()->diffInMinutes($lastActivity) > 5) {
            session(['last_activity' => now()]);
        }

        return $next($request);
    }
}
