<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',  // This loads your channels.php automatically
        health: '/up',
        then: function () {
            // This registers the /broadcasting/auth endpoint (critical for private channels)
            \Illuminate\Support\Facades\Broadcast::routes(['middleware' => ['auth']]);
            // No need to require channels.php again — already handled above
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: ['*']);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // phpcs:disable
        // Custom exception handling can be configured here
        // phpcs:enable
    })->create();
