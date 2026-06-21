<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Jobs\CreateTenantJob;
use App\Mail\InvitationMail;
use App\Models\AddOn;
use App\Models\ClientUser;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tenant::on('servit')->with('plan');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($planId = $request->input('plan_id')) {
            $query->where('plan_id', $planId);
        }

        $tenants = $query->latest()->paginate(20)->withQueryString();
        $plans   = Plan::on('servit')->where('is_active', true)->orderBy('price_monthly_cents')->get();

        return view('superadmin.tenants.index', compact('tenants', 'plans'));
    }

    public function sendInvite(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $invitation = Invitation::create([
            'email'      => $request->email,
            'token'      => Str::random(48),
            'invited_by' => app('auth')->guard('superadmin')->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::send(new InvitationMail($invitation));

        $link = route('portal.invite', $invitation->token);

        return back()->with('invite_link', $link)->with('success', "Invitation sent to {$invitation->email}");
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load('plan', 'invitation');

        $plans      = Plan::on('servit')->where('is_active', true)->orderBy('price_monthly_cents')->get();
        $clientUser = ClientUser::on('servit')->where('tenant_id', $tenant->id)->first();

        $allAddons    = AddOn::on('servit')->where('is_active', true)->orderBy('name')->get();
        $activeAddons = DB::connection('servit')
            ->table('tenant_add_ons')
            ->where('tenant_id', $tenant->id)
            ->whereNull('deactivated_at')
            ->pluck('add_on_id')
            ->flip();

        return view('superadmin.tenants.show', compact('tenant', 'plans', 'clientUser', 'allAddons', 'activeAddons'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validate([
            'plan_id'                     => ['nullable', 'exists:servit.plans,id'],
            'status'                      => ['required', 'in:pending,active,past_due,suspended,cancelled'],
            'overage_fee_per_order_cents' => ['nullable', 'integer', 'min:0'],
            'notes'                       => ['nullable', 'string', 'max:2000'],
        ]);

        $tenant->update($data);

        return back()->with('success', 'Client updated.');
    }

    public function toggleAddon(Tenant $tenant, AddOn $addon): RedirectResponse
    {
        $existing = DB::connection('servit')
            ->table('tenant_add_ons')
            ->where('tenant_id', $tenant->id)
            ->where('add_on_id', $addon->id)
            ->first();

        if (! $existing) {
            DB::connection('servit')->table('tenant_add_ons')->insert([
                'tenant_id'    => $tenant->id,
                'add_on_id'    => $addon->id,
                'quantity'     => 1,
                'activated_at' => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            $msg = "{$addon->name} activated.";
        } elseif ($existing->deactivated_at) {
            DB::connection('servit')->table('tenant_add_ons')
                ->where('tenant_id', $tenant->id)
                ->where('add_on_id', $addon->id)
                ->update(['deactivated_at' => null, 'activated_at' => now(), 'updated_at' => now()]);
            $msg = "{$addon->name} activated.";
        } else {
            DB::connection('servit')->table('tenant_add_ons')
                ->where('tenant_id', $tenant->id)
                ->where('add_on_id', $addon->id)
                ->update(['deactivated_at' => now(), 'updated_at' => now()]);
            $msg = "{$addon->name} deactivated.";
        }

        return back()->with('success', $msg);
    }

    public function reprovision(Tenant $tenant): RedirectResponse
    {
        if ($tenant->db_provisioned) {
            return back()->with('error', 'Tenant is already provisioned.');
        }

        // Drop the partial DB so the job starts from a clean slate
        DB::connection('servit')->statement("DROP DATABASE IF EXISTS `{$tenant->db_name}`");

        $clientUser = ClientUser::on('servit')->where('tenant_id', $tenant->id)->first();

        CreateTenantJob::dispatch($tenant, $clientUser);

        return back()->with('success', 'Provisioning job dispatched. Refresh in a moment.');
    }

    public function accessTenant(Tenant $tenant): RedirectResponse
    {
        $token = Str::random(32);
        Cache::put("servit_access:{$token}", $tenant->id, 60);

        return redirect("https://{$tenant->slug}." . config('app.root_domain') . "/admin/dashboard?servit_token={$token}");
    }
}
