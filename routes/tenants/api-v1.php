<?php

use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Setup\BankController;
use App\Http\Controllers\V1\Setup\CustomerController;
use App\Http\Controllers\V1\Setup\ExternalServiceController;
use App\Http\Controllers\V1\Setup\ServiceController;
use App\Http\Controllers\V1\Setup\SupplierController;
use App\Http\Controllers\V1\Setup\UserController;
use App\Http\Controllers\V1\Setup\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('/login', LoginController::class)->name('login');

Route::middleware([
    'auth:sanctum',
    // 'tenant.api' 
    ])->group(function () {
    

    Route::prefix('setup')->name('setup.')->group(function () {

        Route::apiResource('users', UserController::class)->middleware('module:users');
        Route::apiResource('banks', BankController::class)->middleware('module:banks');
        Route::apiResource('customers', CustomerController::class)->middleware('module:customers');
        Route::apiResource('suppliers', SupplierController::class)->middleware('module:suppliers');
        Route::apiResource('warehouses', WarehouseController::class)->middleware('module:warehouses');
        Route::apiResource('services', ServiceController::class)->middleware('module:services');
        Route::apiResource('externalServices', ExternalServiceController::class)->middleware('module:externalServices');
        
        
    });

});
