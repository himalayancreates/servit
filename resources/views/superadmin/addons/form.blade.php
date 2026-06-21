@extends('superadmin.layouts.app')

@section('title', $addon->exists ? 'Edit Add-on' : 'New Add-on')

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('dashboard.addons.index') }}" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $addon->exists ? 'Edit ' . $addon->name : 'New add-on' }}</h1>
</div>

<form method="POST"
      action="{{ $addon->exists ? route('dashboard.addons.update', $addon) : route('dashboard.addons.store') }}"
      class="max-w-xl">
    @csrf
    @if($addon->exists) @method('PUT') @endif

    @if($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 space-y-1">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 mb-6">
        <div class="px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Details</h2>
        </div>
        <div class="px-6 py-5 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required
                    value="{{ old('name', $addon->name) }}"
                    placeholder="Remove branding"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="description"
                    value="{{ old('description', $addon->description) }}"
                    placeholder="Short description shown to clients"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price / month ($)</label>
                    <input type="number" name="price_dollars" required min="0" step="0.01"
                        value="{{ old('price_dollars', $addon->exists ? $addon->price_cents / 100 : '') }}"
                        placeholder="9.00"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Billing type</label>
                    <select name="billing_type"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                        <option value="flat"    {{ old('billing_type', $addon->billing_type) === 'flat'    ? 'selected' : '' }}>Flat (per month)</option>
                        <option value="metered" {{ old('billing_type', $addon->billing_type) === 'metered' ? 'selected' : '' }}>Metered (per unit)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100 mb-6">
        <div class="px-6 py-4">
            <h2 class="text-sm font-semibold text-gray-700">Stripe IDs</h2>
            <p class="text-xs text-gray-500 mt-0.5">Optional — fill in when wiring up billing</p>
        </div>
        <div class="px-6 py-5 grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Product ID</label>
                <input type="text" name="stripe_product_id"
                    value="{{ old('stripe_product_id', $addon->stripe_product_id) }}"
                    placeholder="prod_..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Price ID</label>
                <input type="text" name="stripe_price_id"
                    value="{{ old('stripe_price_id', $addon->stripe_price_id) }}"
                    placeholder="price_..."
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-6 py-5 mb-8 flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-700">Active</p>
            <p class="text-xs text-gray-500 mt-0.5">Inactive add-ons are hidden from clients</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                {{ old('is_active', $addon->is_active ?? true) ? 'checked' : '' }}>
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
            {{ $addon->exists ? 'Save changes' : 'Create add-on' }}
        </button>
        <a href="{{ route('dashboard.addons.index') }}"
           class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
            Cancel
        </a>
    </div>
</form>

@endsection
