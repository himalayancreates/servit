<?php

use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\TenantsController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('superadmin.login'));

// Super admin portal — only on root domain (no subdomain)
Route::prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        // Guest routes
        Route::middleware('guest:superadmin')->group(function () {
            Route::get('login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('login', [AuthController::class, 'login']);
        });

        // Authenticated routes
        Route::middleware('auth:superadmin')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('clients', [TenantsController::class, 'index'])->name('tenants.index');
            Route::post('clients/invite', [TenantsController::class, 'sendInvite'])->name('tenants.invite');
            Route::get('clients/{tenant}/access', [TenantsController::class, 'accessTenant'])->name('tenants.access');
        });
    });
