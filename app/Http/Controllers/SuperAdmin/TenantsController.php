<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantsController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::on('servit')->with('plan')->latest()->paginate(20);
        $plans   = Plan::on('servit')->where('is_active', true)->get();

        return view('superadmin.tenants.index', compact('tenants', 'plans'));
    }

    public function sendInvite(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $invitation = Invitation::create([
            'email'      => $request->email,
            'token'      => Str::random(48),
            'invited_by' => Auth::guard('superadmin')->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::send(new InvitationMail($invitation));

        $link = route('portal.invite', $invitation->token);

        return back()->with('invite_link', $link)->with('success', "Invitation sent to {$invitation->email}");
    }

    public function accessTenant(Tenant $tenant): RedirectResponse
    {
        // Generate a single-use token valid for 60 seconds
        $token = Str::random(32);
        Cache::put("servit_access:{$token}", $tenant->id, 60);

        return redirect("https://{$tenant->slug}." . config('app.root_domain') . "/admin?servit_token={$token}");
    }
}
