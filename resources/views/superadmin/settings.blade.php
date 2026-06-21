@extends('superadmin.layouts.app')

@section('title', 'Settings')

@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
    <p class="text-sm text-gray-500 mt-1">Platform-wide configuration</p>
</div>

<div class="max-w-2xl space-y-6">

    {{-- Platform --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        <div class="px-6 py-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Platform</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded px-2 py-0.5">Coming soon</span>
        </div>
        <div class="px-6 py-5 space-y-4 opacity-50 pointer-events-none">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Root domain</label>
                <input type="text" disabled value="{{ config('app.root_domain', 'servit.app') }}"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Platform name</label>
                <input type="text" disabled value="ServIt"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
            </div>
        </div>
    </div>

    {{-- Email --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        <div class="px-6 py-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Email</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded px-2 py-0.5">Coming soon</span>
        </div>
        <div class="px-6 py-5 space-y-4 opacity-50 pointer-events-none">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From address</label>
                <input type="email" disabled value="hello@servit.app"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From name</label>
                <input type="text" disabled value="ServIt"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
            </div>
        </div>
    </div>

    {{-- Stripe --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        <div class="px-6 py-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Stripe</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded px-2 py-0.5">Coming soon</span>
        </div>
        <div class="px-6 py-5 space-y-4 opacity-50 pointer-events-none">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Publishable key</label>
                <input type="text" disabled value="pk_live_..."
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-mono text-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Webhook secret</label>
                <input type="text" disabled value="whsec_..."
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-mono text-gray-500">
            </div>
        </div>
    </div>

    {{-- Provisioning --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        <div class="px-6 py-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Tenant provisioning</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded px-2 py-0.5">Coming soon</span>
        </div>
        <div class="px-6 py-5 space-y-4 opacity-50 pointer-events-none">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default DB host</label>
                <input type="text" disabled value="{{ env('TENANT_DB_HOST', '127.0.0.1') }}"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-mono text-gray-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Invitation expiry (days)</label>
                <input type="number" disabled value="7"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500">
            </div>
        </div>
    </div>

</div>

@endsection
