<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AddOnsController extends Controller
{
    public function index(): View
    {
        $addons = AddOn::on('servit')->withCount('tenants')->orderBy('name')->get();

        return view('superadmin.addons.index', compact('addons'));
    }

    public function create(): View
    {
        return view('superadmin.addons.form', ['addon' => new AddOn()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        AddOn::create($data);

        return redirect()->route('dashboard.addons.index')->with('success', 'Add-on created.');
    }

    public function edit(AddOn $addon): View
    {
        return view('superadmin.addons.form', compact('addon'));
    }

    public function update(Request $request, AddOn $addon): RedirectResponse
    {
        $data = $this->validated($request, $addon);

        $addon->update($data);

        return redirect()->route('dashboard.addons.index')->with('success', 'Add-on updated.');
    }

    public function destroy(AddOn $addon): RedirectResponse
    {
        if ($addon->tenants()->wherePivotNull('deactivated_at')->exists()) {
            return back()->with('error', 'Cannot delete an add-on with active subscribers. Deactivate it instead.');
        }

        $addon->delete();

        return redirect()->route('dashboard.addons.index')->with('success', 'Add-on deleted.');
    }

    private function validated(Request $request, ?AddOn $addon = null): array
    {
        $data = $request->validate([
            'name'                => ['required', 'string', 'max:100'],
            'billing_type'        => ['required', 'in:flat,metered'],
            'price_dollars'       => ['required', 'numeric', 'min:0'],
            'description'         => ['nullable', 'string', 'max:255'],
            'is_active'           => ['boolean'],
            'stripe_product_id'   => ['nullable', 'string', 'max:255'],
            'stripe_price_id'     => ['nullable', 'string', 'max:255'],
        ]);

        $data['price_cents'] = (int) round($data['price_dollars'] * 100);
        unset($data['price_dollars']);

        $data['is_active'] = $request->boolean('is_active');

        if ($addon === null || ! $addon->exists) {
            $slug = Str::slug($data['name']);
            $base = $slug;
            $i    = 2;
            while (AddOn::on('servit')->where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        return $data;
    }
}
