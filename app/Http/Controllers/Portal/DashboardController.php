<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $client = app('auth')->guard('client')->user();
        $tenant = $client->tenant()->with('plan')->first();
        $plans  = Plan::on('servit')->where('is_active', true)->orderBy('price_monthly_cents')->get();

        $ordersThisMonth = 0;
        $orderLimit      = null;
        $usagePct        = 0;

        if ($tenant->plan) {
            $key             = "tenant:{$tenant->id}:orders:" . now()->format('Y-m');
            $ordersThisMonth = (int) Cache::get($key, 0);
            $orderLimit      = $tenant->plan->order_limit;
            $usagePct        = $orderLimit ? min(100, (int) round(($ordersThisMonth / $orderLimit) * 100)) : 0;
        }

        return view('portal.dashboard', compact(
            'client', 'tenant', 'plans',
            'ordersThisMonth', 'orderLimit', 'usagePct'
        ));
    }

    public function accessAdmin(string $destination = 'admin'): RedirectResponse
    {
        $client = app('auth')->guard('client')->user();
        $tenant = $client->tenant()->first();

        $token = Str::random(32);

        Cache::put("servit_client_access:{$token}", [
            'email' => $client->email,
        ], 60);

        return redirect("https://{$tenant->slug}." . config('app.root_domain') . "/{$destination}?servit_client_token={$token}");
    }
}
