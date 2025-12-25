<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GlobalErrorHandler
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
        try {
            return $next($request);
        } catch (\Exception $e) {
            // Log the error with full details
            Log::error('Global Error Handler: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => session('user_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            // If it's an API request, return JSON error
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'An error occurred while processing your request.',
                    'message' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
                ], 500);
            }

            // For gadgets routes, show the actual error in debug mode
            if ($request->is('gadgets*') && config('app.debug')) {
                return response()->make(
                    '<h1>Error: ' . htmlspecialchars($e->getMessage()) . '</h1>' .
                    '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>',
                    500
                );
            }

            // For web requests, check if user is authenticated
            // If authenticated, try to redirect back, otherwise go to home
            if (session('api_token') || session('user_id')) {
                // Try to redirect back to previous page
                $redirectTo = url()->previous() ?: '/dashboard';
                return redirect($redirectTo)->with('error', 'An error occurred: ' . $e->getMessage());
            }
            
            // Not authenticated, redirect to home
            return redirect('/home')->with('error', 'An error occurred. Please try again.');
        }
    }
}
