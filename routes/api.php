<?php

use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Setup\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('v1.')
    ->group(function () {
    
    Route::post('/login', LoginController::class)->name('login');

    Route::middleware('auth:sanctum')->group(function () {
       
        Route::prefix('setup')->name('setup.')->group(function () {

            Route::apiResource('users', UserController::class);
            
        });

    });
    
});