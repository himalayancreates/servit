<?php

use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Portal\OnboardingController as PortalOnboardingController;
use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\AddOnsController;
use App\Http\Controllers\SuperAdmin\InvitationsController;
use App\Http\Controllers\SuperAdmin\PlansController;
use App\Http\Controllers\SuperAdmin\SettingsController;
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

        // Onboarding
        Route::post('onboarding/plan/{plan}', [PortalOnboardingController::class, 'selectPlan'])->name('onboarding.plan');

        // SSO into tenant TI admin
        Route::get('access/{destination?}', [PortalDashboardController::class, 'accessAdmin'])
            ->where('destination', '.*')
            ->name('access');

        // Placeholder pages (filled in when Stripe is wired)
        Route::get('billing', fn () => view('portal.placeholder', ['title' => 'Billing', 'message' => 'Billing and subscription management is coming soon. Contact us to change your plan.']))->name('billing');
        Route::get('addons', fn () => view('portal.placeholder', ['title' => 'Add-ons', 'message' => 'Add-on management is coming soon.']))->name('addons');
        Route::get('embed', fn () => view('portal.placeholder', ['title' => 'Embed', 'message' => 'Embed options are coming soon.']))->name('embed');
        Route::get('settings', fn () => view('portal.placeholder', ['title' => 'Settings', 'message' => 'Settings are coming soon.']))->name('settings');
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
            Route::get('clients/{tenant}', [TenantsController::class, 'show'])->name('tenants.show');
            Route::put('clients/{tenant}', [TenantsController::class, 'update'])->name('tenants.update');
            Route::get('clients/{tenant}/access', [TenantsController::class, 'accessTenant'])->name('tenants.access');
            Route::post('clients/{tenant}/addons/{addon}/toggle', [TenantsController::class, 'toggleAddon'])->name('tenants.addons.toggle');
            Route::post('clients/{tenant}/reprovision', [TenantsController::class, 'reprovision'])->name('tenants.reprovision');

            Route::get('invitations', [InvitationsController::class, 'index'])->name('invitations.index');
            Route::post('invitations/{invitation}/resend', [InvitationsController::class, 'resend'])->name('invitations.resend');
            Route::post('invitations/{invitation}/revoke', [InvitationsController::class, 'revoke'])->name('invitations.revoke');

            Route::get('settings', [SettingsController::class, 'index'])->name('settings');

            Route::get('addons', [AddOnsController::class, 'index'])->name('addons.index');
            Route::get('addons/create', [AddOnsController::class, 'create'])->name('addons.create');
            Route::post('addons', [AddOnsController::class, 'store'])->name('addons.store');
            Route::get('addons/{addon}/edit', [AddOnsController::class, 'edit'])->name('addons.edit');
            Route::put('addons/{addon}', [AddOnsController::class, 'update'])->name('addons.update');
            Route::delete('addons/{addon}', [AddOnsController::class, 'destroy'])->name('addons.destroy');

            Route::get('plans', [PlansController::class, 'index'])->name('plans.index');
            Route::get('plans/create', [PlansController::class, 'create'])->name('plans.create');
            Route::post('plans', [PlansController::class, 'store'])->name('plans.store');
            Route::get('plans/{plan}/edit', [PlansController::class, 'edit'])->name('plans.edit');
            Route::put('plans/{plan}', [PlansController::class, 'update'])->name('plans.update');
            Route::delete('plans/{plan}', [PlansController::class, 'destroy'])->name('plans.destroy');
        });
    });
