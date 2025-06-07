<?php

use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Setup\BankController;
use App\Http\Controllers\V1\Setup\UserController;
use App\Http\Middleware\InitializeTenant;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->name('login');

Route::middleware([
    'auth:sanctum',
    // 'tenant.api' 
    ])->group(function () {
    

    Route::prefix('setup')->name('setup.')->group(function () {

        Route::apiResource('users', UserController::class);
        Route::apiResource('banks', BankController::class);
        
    });

});
