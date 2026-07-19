<?php

use App\Http\Middleware\GateMiddleware;
use App\Http\Middleware\LogUserActivityMiddleware;
use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'gate' => GateMiddleware::class,
            'maintenance.access' => MaintenanceModeMiddleware::class,
        ]);

        // Register middleware groups
        $middleware->group('admin', [
            'auth',
            'gate:access-admin-panel',
        ]);

        // Register global middleware
        $middleware->web(append: [
            MaintenanceModeMiddleware::class,
            LogUserActivityMiddleware::class,
            SecurityHeadersMiddleware::class,
            'throttle:global',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
