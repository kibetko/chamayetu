<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Global Middleware
        |--------------------------------------------------------------------------
        |
        | This runs on every request.
        | Updates the logged-in user's last_seen_at timestamp.
        |
        */

        $middleware->append([
            \App\Http\Middleware\UpdateLastSeen::class,
        ]);


        /*
        |--------------------------------------------------------------------------
        | CSRF Exceptions
        |--------------------------------------------------------------------------
        |
        | M-Pesa sends callbacks directly to this URL.
        | We exclude it from CSRF verification.
        |
        */

        $middleware->validateCsrfTokens(
            except: [
                'payments/callback',
            ]
        );


    })

    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

    })

    ->create();