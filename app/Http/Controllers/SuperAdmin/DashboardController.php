<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $planMrrCents = DB::connection('servit')
            ->table('tenants')
            ->join('plans', 'tenants.plan_id', '=', 'plans.id')
            ->where('tenants.status', 'active')
            ->sum('plans.price_monthly_cents');

        $addonMrrCents = DB::connection('servit')
            ->table('tenant_add_ons')
            ->join('add_ons', 'tenant_add_ons.add_on_id', '=', 'add_ons.id')
            ->join('tenants', 'tenant_add_ons.tenant_id', '=', 'tenants.id')
            ->where('tenants.status', 'active')
            ->whereNull('tenant_add_ons.deactivated_at')
            ->sum(DB::raw('add_ons.price_cents * COALESCE(tenant_add_ons.quantity, 1)'));

        $totalInvites    = Invitation::on('servit')->count();
        $acceptedInvites = Invitation::on('servit')->whereNotNull('accepted_at')->count();
        $conversionRate  = $totalInvites > 0 ? round(($acceptedInvites / $totalInvites) * 100) : 0;

        $stats = [
            'mrr'             => '$' . number_format(($planMrrCents + $addonMrrCents) / 100, 0),
            'mrr_plans'       => '$' . number_format($planMrrCents / 100, 0),
            'mrr_addons'      => '$' . number_format($addonMrrCents / 100, 0),
            'total_tenants'   => Tenant::on('servit')->count(),
            'active_tenants'  => Tenant::on('servit')->where('status', 'active')->count(),
            'new_this_month'  => Tenant::on('servit')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'pending'         => Tenant::on('servit')->where('status', 'pending')->count(),
            'past_due'        => Tenant::on('servit')->where('status', 'past_due')->count(),
            'suspended'       => Tenant::on('servit')->where('status', 'suspended')->count(),
            'conversion_rate' => $conversionRate,
            'invites_sent'    => $totalInvites,
            'invites_accepted' => $acceptedInvites,
        ];

        $recentTenants = Tenant::on('servit')
            ->with('plan')
            ->latest()
            ->take(8)
            ->get();

        $plans = Plan::on('servit')
            ->withCount('tenants')
            ->orderBy('price_monthly_cents')
            ->get();

        $topAddons = DB::connection('servit')
            ->table('add_ons')
            ->leftJoin('tenant_add_ons', function ($join) {
                $join->on('add_ons.id', '=', 'tenant_add_ons.add_on_id')
                     ->whereNull('tenant_add_ons.deactivated_at');
            })
            ->select('add_ons.name', DB::raw('COUNT(tenant_add_ons.tenant_id) as active_count'))
            ->where('add_ons.is_active', true)
            ->groupBy('add_ons.id', 'add_ons.name')
            ->orderByDesc('active_count')
            ->limit(8)
            ->get();

        $pendingInvites = Invitation::on('servit')
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->take(5)
            ->get();

        $recentActivity = $this->recentActivity();

        $stuckTenants = Tenant::on('servit')
            ->where('db_provisioned', false)
            ->where('created_at', '<', now()->subMinutes(10))
            ->get();

        return view('superadmin.dashboard', compact(
            'stats', 'recentTenants', 'plans', 'topAddons',
            'pendingInvites', 'recentActivity', 'stuckTenants'
        ));
    }

    private function recentActivity(): array
    {
        $signups = Tenant::on('servit')
            ->with('plan')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($t) => [
                'type'  => 'signup',
                'label' => "{$t->name} signed up",
                'sub'   => $t->plan?->name ?? 'No plan',
                'at'    => $t->created_at,
            ]);

        $invites = Invitation::on('servit')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($i) => [
                'type'  => $i->accepted_at ? 'accepted' : 'invited',
                'label' => $i->accepted_at
                    ? "{$i->email} accepted invite"
                    : "Invite sent to {$i->email}",
                'sub'   => $i->accepted_at
                    ? 'Accepted ' . $i->accepted_at->diffForHumans()
                    : 'Expires ' . $i->expires_at->diffForHumans(),
                'at'    => $i->accepted_at ?? $i->created_at,
            ]);

        return $signups->concat($invites)
            ->sortByDesc('at')
            ->take(8)
            ->values()
            ->all();
    }
}
