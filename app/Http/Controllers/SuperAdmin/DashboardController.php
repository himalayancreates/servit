<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_tenants'  => Tenant::on('servit')->count(),
            'active_tenants' => Tenant::on('servit')->where('status', 'active')->count(),
            'pending'        => Tenant::on('servit')->where('status', 'pending')->count(),
            'past_due'       => Tenant::on('servit')->where('status', 'past_due')->count(),
        ];

        $recentTenants = Tenant::on('servit')
            ->with('plan')
            ->latest()
            ->take(5)
            ->get();

        $plans = Plan::on('servit')->withCount('tenants')->get();

        return view('superadmin.dashboard', compact('stats', 'recentTenants', 'plans'));
    }
}
