<?php

use App\Http\Controllers\V1\Advance\PermissionController;
use App\Http\Controllers\V1\Advance\RoleController;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\LogoutController;
use App\Http\Controllers\V1\Auth\TenantInfoController;
use App\Http\Controllers\V1\Setup\BankController;
use App\Http\Controllers\V1\Setup\CustomerController;
use App\Http\Controllers\V1\Setup\ExpenseArticleController;
use App\Http\Controllers\V1\Setup\ExpenseTypeController;
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
    
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions');
    Route::post('permissions/sync', [PermissionController::class, 'syncPermissions'])->name('permissions.sync');

    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{role}/deactivate', [RoleController::class, 'deactivate']);
    Route::get('permission-groups', [RoleController::class, 'permissionGroups']);
    Route::patch('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus']);
    Route::get('available-modules', [TenantInfoController::class, 'availableModules'])->name('tenant.modules');
    Route::get('profile', [TenantInfoController::class, 'show'])->name('profile.show');

    Route::prefix('setup')->name('setup.')->group(function () {

        Route::apiResource('users', UserController::class)->middleware('module:users');
        Route::apiResource('banks', BankController::class)->middleware('module:banks');
        Route::apiResource('customers', CustomerController::class)->middleware('module:customers');

        Route::prefix('suppliers')->name('suppliers.')->controller(SupplierController::class)->middleware('module:suppliers')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::get('active', 'active')->name('active');
            Route::get('trashed', 'trashed')->name('trashed');
            Route::apiResource('', SupplierController::class)->parameters(['' => 'supplier']);
        });

        Route::apiResource('warehouses', WarehouseController::class)->middleware('module:warehouses');
        Route::apiResource('services', ServiceController::class)->middleware('module:services');
        Route::apiResource('externalServices', ExternalServiceController::class)->middleware('module:externalServices');
        

        Route::prefix('expense-types')->name('expense-type.')->controller(ExpenseTypeController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/tree', 'tree')->name('tree');
            Route::get('/{expense_type}', 'show')->name('show');
            Route::put('/{expense_type}', 'update')->name('update');
            Route::delete('/{expense_type}', 'destroy')->name('destroy');
            Route::post('/{expense_type}/restore', 'restore')->name('restore');
            Route::delete('/{expense_type}/force', 'forceDelete')->name('force');
            Route::patch('/{expense_type}/toggle-status', 'toggleStatus')->name('toggleStatus');
        })->middleware('module:expense_types');

        Route::prefix('expense-articles')->name('expense-articles.')->controller(ExpenseArticleController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/tree', 'tree')->name('tree');
            Route::get('/{expense_article}', 'show')->name('show');
            Route::put('/{expense_article}', 'update')->name('update');
            Route::delete('/{expense_article}', 'destroy')->name('destroy');
            Route::post('/{expense_article}/restore', 'restore')->name('restore');
            Route::delete('/{expense_article}/force', 'forceDelete')->name('force');
            Route::patch('/{expense_article}/toggle-status', 'toggleStatus')->name('toggleStatus');
        })->middleware('module:expense_articles');
        
    });

});
