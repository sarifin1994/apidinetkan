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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'multi_auth' => \App\Http\Middleware\MultiAuthMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'status' => \App\Http\Middleware\StatusMiddleware::class,
            'check.api.key.ext' => \App\Http\Middleware\CheckApiKeyExt::class,
        ]);

        $middleware->validateCsrfTokens(except: [    
            '/midtrans/notification',
            '/duitku/notification',
           ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
