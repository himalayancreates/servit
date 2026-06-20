<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use App\Models\Invitation;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (app('auth')->guard('client')->check()) {
            return redirect()->route('portal.home');
        }

        return view('portal.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (app('auth')->guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('portal.home'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        app('auth')->guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }

    // Step 1 of invite acceptance — show the setup form
    public function showSetup(string $token): View|RedirectResponse
    {
        $invitation = Invitation::on('servit')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('portal.auth.setup', compact('invitation', 'token'));
    }

    // Step 2 — create tenant + client user, log them in
    public function setup(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::on('servit')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $data = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:100'],
            'name'            => ['required', 'string', 'max:100'],
            'password'        => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $slug = Str::slug($data['restaurant_name']);

        // Ensure slug is unique
        $base = $slug;
        $i = 2;
        while (Tenant::on('servit')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $tenant = Tenant::create([
            'name'          => $data['restaurant_name'],
            'slug'          => $slug,
            'status'        => 'trialing',
            'trial_ends_at' => now()->addDays(14),
            'invitation_id' => $invitation->id,
            'db_name'       => 'tenant_' . ($invitation->id),
            'db_host'       => env('TENANT_DB_HOST', '127.0.0.1'),
        ]);

        $client = ClientUser::create([
            'tenant_id' => $tenant->id,
            'name'      => $data['name'],
            'email'     => $invitation->email,
            'password'  => $data['password'],
        ]);

        $invitation->update(['accepted_at' => now()]);

        app('auth')->guard('client')->login($client);
        $request->session()->regenerate();

        return redirect()->route('portal.home');
    }
}
