<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlansController extends Controller
{
    public function index(): View
    {
        $plans = Plan::on('servit')->withCount('tenants')->orderBy('price_monthly_cents')->get();

        return view('superadmin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('superadmin.plans.form', ['plan' => new Plan()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Plan::create($data);

        return redirect()->route('dashboard.plans.index')->with('success', 'Plan created.');
    }

    public function edit(Plan $plan): View
    {
        return view('superadmin.plans.form', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validated($request, $plan);

        $plan->update($data);

        return redirect()->route('dashboard.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->tenants()->exists()) {
            return back()->with('error', 'Cannot delete a plan with active clients. Deactivate it instead.');
        }

        $plan->delete();

        return redirect()->route('dashboard.plans.index')->with('success', 'Plan deleted.');
    }

    private function validated(Request $request, ?Plan $plan = null): array
    {
        $data = $request->validate([
            'name'                        => ['required', 'string', 'max:100'],
            'order_limit'                 => ['nullable', 'integer', 'min:1'],
            'rate_limit_per_hour'         => ['nullable', 'integer', 'min:1'],
            'locations_included'          => ['required', 'integer', 'min:1'],
            'platform_fee_percent'        => ['required', 'numeric', 'min:0', 'max:100'],
            'price_monthly_dollars'       => ['required', 'numeric', 'min:0'],
            'overage_fee_per_order_cents' => ['required', 'integer', 'min:0'],
            'is_active'                   => ['boolean'],
            'stripe_product_id'           => ['nullable', 'string', 'max:255'],
            'stripe_price_id'             => ['nullable', 'string', 'max:255'],
        ]);

        $data['price_monthly_cents'] = (int) round($data['price_monthly_dollars'] * 100);
        unset($data['price_monthly_dollars']);

        $data['is_active'] = $request->boolean('is_active');

        // Only generate slug on create
        if ($plan === null || ! $plan->exists) {
            $slug = Str::slug($data['name']);
            $base = $slug;
            $i    = 2;
            while (Plan::on('servit')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        return $data;
    }
}
