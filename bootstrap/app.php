<?php

use App\Http\Middleware\InitializeTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

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
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
