<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Exclude API routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
        
        $middleware->alias([
            'web.auth' => \App\Http\Middleware\AuthMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeoutMiddleware::class,
            'no.cache' => \App\Http\Middleware\NoCacheMiddleware::class,
            'global.error' => \App\Http\Middleware\GlobalErrorHandler::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
