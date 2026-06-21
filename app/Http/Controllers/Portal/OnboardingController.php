<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;

class OnboardingController extends Controller
{
    public function selectPlan(Plan $plan): RedirectResponse
    {
        $client = app('auth')->guard('client')->user();
        $tenant = $client->tenant()->first();

        if ($tenant->status !== 'pending') {
            return redirect()->route('portal.billing')
                ->with('error', 'To change your plan, use the Billing page.');
        }

        $tenant->update([
            'plan_id' => $plan->id,
            'status'  => 'active',
        ]);

        return redirect()->route('portal.home')
            ->with('success', "You're on the {$plan->name} plan — your restaurant is live!");
    }
}
