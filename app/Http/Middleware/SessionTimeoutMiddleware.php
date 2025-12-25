<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in (admin or buyer)
        if (session('api_token') || session('user_id')) {
            $lastActivity = session('last_activity');
            $sessionTimeout = 480; // 8 hours in minutes (more lenient)
            
            // Only check timeout if we have a last activity time
            if ($lastActivity && now()->diffInMinutes($lastActivity) > $sessionTimeout) {
                // Clear session and redirect to login
                session()->flush();
                return redirect('/login')->with('error', 'Your session has expired. Please login again.');
            }
            
            // Update last activity time only if it's been more than 5 minutes
            // This prevents constant session updates on every request
            if (!$lastActivity || now()->diffInMinutes($lastActivity) > 5) {
                session(['last_activity' => now()]);
            }
        }

        return $next($request);
    }
}
