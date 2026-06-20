<?php

use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\TenantsController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard.login'));

// Client portal
Route::prefix('portal')->name('portal.')->group(function () {
    // Invite acceptance (no auth required)
    Route::get('invite/{token}', [PortalAuthController::class, 'showSetup'])->name('invite');
    Route::post('invite/{token}', [PortalAuthController::class, 'setup']);

    // Guest-only
    Route::middleware('guest:client')->group(function () {
        Route::get('login', [PortalAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [PortalAuthController::class, 'login']);
    });

    // Authenticated client routes
    Route::middleware('auth:client')->group(function () {
        Route::post('logout', [PortalAuthController::class, 'logout'])->name('logout');
        Route::get('/', [PortalDashboardController::class, 'index'])->name('home');
    });
});

// Super admin portal — only on root domain (no subdomain)
Route::prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        // Guest routes
        Route::middleware('guest:superadmin')->group(function () {
            Route::get('login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('login', [AuthController::class, 'login']);
        });

        // Authenticated routes
        Route::middleware('auth:superadmin')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/', [DashboardController::class, 'index'])->name('home');
            Route::get('clients', [TenantsController::class, 'index'])->name('tenants.index');
            Route::post('clients/invite', [TenantsController::class, 'sendInvite'])->name('tenants.invite');
            Route::get('clients/{tenant}/access', [TenantsController::class, 'accessTenant'])->name('tenants.access');
        });
    });
