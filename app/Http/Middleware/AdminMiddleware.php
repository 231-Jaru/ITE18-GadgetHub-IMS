<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is admin
        // Note: Authentication is already checked by AuthMiddleware which runs before this
        $userType = session('user_type');
        if ($userType !== 'admin') {
            return redirect('/home')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
