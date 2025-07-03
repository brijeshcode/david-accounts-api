<?php

use App\Http\Middleware\CheckTenantModule;
use App\Http\Middleware\InitializeTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function() {
            Route::
            middleware('api')
            ->prefix('api/v1/')
            ->name('v1.')
            ->group(base_path('routes/tenants/api-v1.php'));

        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // InitializeTenant::class;
        $middleware->append([
            InitializeTenant::class,
        ]);

        $middleware->alias([
            'module' => CheckTenantModule::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
