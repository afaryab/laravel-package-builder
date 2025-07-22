<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Gate;
use LaravelApp\Models\User;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.type' => LaravelApp\Http\Middleware\AuthTypeMiddleware::class,
            'auth.basic' => LaravelApp\Http\Middleware\BasicAuthMiddleware::class,
            'token.auth' => LaravelApp\Http\Middleware\TokenAuthMiddleware::class,
            'auth.dynamic' => LaravelApp\Http\Middleware\AuthenticateWithAuthType::class,
            'log.activity' => LaravelApp\Http\Middleware\LogActivity::class,
            'permission' => LaravelApp\Http\Middleware\CheckPermission::class,
            'disable.auth.routes' => LaravelApp\Http\Middleware\DisableAuthRelatedRoutes::class,
        ]);
        
        // Add LogActivity middleware globally to capture all requests
        $middleware->append(LaravelApp\Http\Middleware\LogActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
