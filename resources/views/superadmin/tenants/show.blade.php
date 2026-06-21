@extends('superadmin.layouts.app')

@section('title', $tenant->name)

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('dashboard.tenants.index') }}" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div class="flex-1">
        <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            <a href="https://{{ $tenant->slug }}.{{ config('app.root_domain') }}" target="_blank"
               class="text-orange-500 hover:underline">{{ $tenant->slug }}.{{ config('app.root_domain') }}</a>
            · joined {{ $tenant->created_at->format('M d, Y') }}
        </p>
    </div>
    <a href="{{ route('dashboard.tenants.access', $tenant) }}"
       target="_blank"
       class="inline-flex items-center gap-2 rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
        Open Admin
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
    </a>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Left column: editable settings --}}
    <div class="col-span-2 space-y-6">

        <form method="POST" action="{{ route('dashboard.tenants.update', $tenant) }}">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
            @endif

            {{-- Status & plan --}}
            <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                <div class="px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Subscription</h2>
                </div>
                <div class="px-6 py-5 grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            @foreach(['pending','active','past_due','suspended','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $tenant->status === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                        <select name="plan_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                            <option value="">— No plan —</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ $tenant->plan_id === $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ $plan->price_monthly }}/mo)
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Custom billing --}}
            <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                <div class="px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Custom billing</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Overrides plan defaults for this client</p>
                </div>
                <div class="px-6 py-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Overage fee per order (cents)
                        <span class="ml-1 font-normal text-gray-400">
                            — plan default: {{ $tenant->plan?->overage_fee_per_order_cents ?? '—' }}¢
                        </span>
                    </label>
                    <input type="number" name="overage_fee_per_order_cents" min="0"
                        value="{{ old('overage_fee_per_order_cents', $tenant->overage_fee_per_order_cents) }}"
                        placeholder="Leave blank to use plan default"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                <div class="px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-700">Internal notes</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Custom deals, discounts applied, reminders — not visible to client</p>
                </div>
                <div class="px-6 py-5">
                    <textarea name="notes" rows="4"
                        placeholder="e.g. Applied 20% Stripe coupon — agreed to review in Jan 2027"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">{{ old('notes', $tenant->notes) }}</textarea>
                </div>
            </div>

            <button type="submit"
                class="rounded-lg bg-orange-500 hover:bg-orange-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors">
                Save changes
            </button>
        </form>

    </div>

    {{-- Right column: read-only info --}}
    <div class="space-y-6">

        {{-- Client user --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Account owner</h2>
            </div>
            <div class="px-6 py-5 space-y-3">
                @if($clientUser)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-sm font-bold shrink-0">
                        {{ strtoupper(substr($clientUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $clientUser->name }}</p>
                        <p class="text-xs text-gray-500">{{ $clientUser->email }}</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-400">No account set up yet</p>
                @endif
            </div>
        </div>

        {{-- Database --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Infrastructure</h2>
            </div>
            <div class="px-6 py-5 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Database</span>
                    <span class="font-mono text-xs text-gray-700">{{ $tenant->db_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">DB host</span>
                    <span class="font-mono text-xs text-gray-700">{{ $tenant->db_host }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Provisioned</span>
                    <span class="{{ $tenant->db_provisioned ? 'text-green-600' : 'text-amber-600' }} font-medium text-xs">
                        {{ $tenant->db_provisioned ? 'Yes' : 'Pending' }}
                    </span>
                </div>
            </div>
            @if(!$tenant->db_provisioned)
            <div class="px-6 py-4 border-t border-gray-100">
                <form method="POST" action="{{ route('dashboard.tenants.reprovision', $tenant) }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-lg bg-amber-500 hover:bg-amber-600 px-4 py-2 text-xs font-semibold text-white transition-colors">
                        Re-run provisioning
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Stripe --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Stripe</h2>
            </div>
            <div class="px-6 py-5 space-y-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Customer ID</p>
                    <p class="font-mono text-xs text-gray-700">{{ $tenant->stripe_customer_id ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Connect account</p>
                    <p class="font-mono text-xs text-gray-700">{{ $tenant->stripe_connect_account_id ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Invitation --}}
        @if($tenant->invitation)
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Invite</h2>
            </div>
            <div class="px-6 py-5 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Sent to</span>
                    <span class="text-gray-700">{{ $tenant->invitation->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Accepted</span>
                    <span class="text-gray-700">{{ $tenant->invitation->accepted_at?->format('M d, Y') ?? '—' }}</span>
                </div>
            </div>
        </div>
        @endif

    </div>

</div>

{{-- Add-ons (full width) --}}
@if($allAddons->count())
<div class="mt-6 bg-white rounded-xl border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Add-ons</h2>
        <p class="text-xs text-gray-500 mt-0.5">Click to activate or deactivate for this client</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 divide-y divide-gray-50">
        @foreach($allAddons as $addon)
        @php $isActive = isset($activeAddons[$addon->id]); @endphp
        <form method="POST" action="{{ route('dashboard.tenants.addons.toggle', [$tenant, $addon]) }}"
              class="flex items-center justify-between px-6 py-4 border-b border-r border-gray-50 hover:bg-gray-50 transition-colors">
            @csrf
            <div class="min-w-0 mr-4">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $addon->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $addon->price }}/mo
                    @if($addon->billing_type === 'metered')
                    <span class="text-gray-300">· metered</span>
                    @endif
                </p>
            </div>
            <button type="submit" title="{{ $isActive ? 'Deactivate' : 'Activate' }}"
                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 {{ $isActive ? 'bg-orange-500' : 'bg-gray-200' }}">
                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 {{ $isActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
            </button>
        </form>
        @endforeach
    </div>
</div>
@endif

@endsection
