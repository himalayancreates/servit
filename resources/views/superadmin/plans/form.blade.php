@extends('superadmin.layouts.app')

@section('title', $plan->exists ? 'Edit Plan' : 'New Plan')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('dashboard.plans.index') }}" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $plan->exists ? 'Edit ' . $plan->name : 'New plan' }}</h1>
        @if($plan->exists)
        <p class="text-sm text-gray-500 mt-1">{{ $plan->tenants_count ?? 0 }} client(s) on this plan</p>
        @endif
    </div>
</div>

<form method="POST"
      action="{{ $plan->exists ? route('dashboard.plans.update', $plan) : route('dashboard.plans.store') }}"
      class="max-w-2xl">
    @csrf
    @if($plan->exists) @method('PUT') @endif

    @if($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 space-y-1">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- Core details --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 mb-6">
        <div class="px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Plan details</h2>
        </div>

        <div class="px-6 py-5 grid grid-cols-2 gap-5">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan name</label>
                <input type="text" name="name" required
                    value="{{ old('name', $plan->name) }}"
                    placeholder="Starter"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price / month ($)</label>
                <input type="number" name="price_monthly_dollars" required min="0" step="0.01"
                    value="{{ old('price_monthly_dollars', $plan->exists ? $plan->price_monthly_cents / 100 : '') }}"
                    placeholder="29.00"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Locations included</label>
                <input type="number" name="locations_included" required min="1"
                    value="{{ old('locations_included', $plan->locations_included ?? 1) }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>
        </div>
    </div>

    {{-- Limits --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 mb-6">
        <div class="px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Limits</h2>
            <p class="text-xs text-gray-500 mt-0.5">Leave blank for unlimited</p>
        </div>

        <div class="px-6 py-5 grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orders / month</label>
                <input type="number" name="order_limit" min="1"
                    value="{{ old('order_limit', $plan->order_limit) }}"
                    placeholder="Unlimited"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orders / hour (rate limit)</label>
                <input type="number" name="rate_limit_per_hour" min="1"
                    value="{{ old('rate_limit_per_hour', $plan->rate_limit_per_hour) }}"
                    placeholder="Unlimited"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>
        </div>
    </div>

    {{-- Billing --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 mb-6">
        <div class="px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Billing</h2>
        </div>

        <div class="px-6 py-5 grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Platform fee (%)</label>
                <input type="number" name="platform_fee_percent" required min="0" max="100" step="0.01"
                    value="{{ old('platform_fee_percent', $plan->platform_fee_percent ?? 1.00) }}"
                    placeholder="1.00"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                <p class="mt-1 text-xs text-gray-500">Charged on each customer order via Stripe Connect</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Overage fee per order (cents)</label>
                <input type="number" name="overage_fee_per_order_cents" required min="0"
                    value="{{ old('overage_fee_per_order_cents', $plan->overage_fee_per_order_cents ?? 10) }}"
                    placeholder="10"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                <p class="mt-1 text-xs text-gray-500">Charged for each order over the monthly limit</p>
            </div>
        </div>
    </div>

    {{-- Stripe (optional) --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 mb-6">
        <div class="px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Stripe IDs</h2>
            <p class="text-xs text-gray-500 mt-0.5">Optional — fill in when wiring up billing</p>
        </div>

        <div class="px-6 py-5 grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Product ID</label>
                <input type="text" name="stripe_product_id"
                    value="{{ old('stripe_product_id', $plan->stripe_product_id) }}"
                    placeholder="prod_..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Price ID</label>
                <input type="text" name="stripe_price_id"
                    value="{{ old('stripe_price_id', $plan->stripe_price_id) }}"
                    placeholder="price_..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>
        </div>
    </div>

    {{-- Active toggle --}}
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-5 mb-8 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-700">Active</p>
            <p class="text-xs text-gray-500 mt-0.5">Inactive plans are hidden from clients</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}>
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                peer-checked:after:translate-x-full peer-checked:after:border-white
                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                after:bg-white after:border-gray-300 after:border after:rounded-full
                after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-lg bg-orange-500 hover:bg-orange-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors">
            {{ $plan->exists ? 'Save changes' : 'Create plan' }}
        </button>
        <a href="{{ route('dashboard.plans.index') }}"
           class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
            Cancel
        </a>
    </div>
</form>

@endsection
