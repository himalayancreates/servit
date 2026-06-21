@extends('portal.layouts.app')

@section('title', $tenant->name)

@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">{{ $tenant->name }}</h1>
    <a href="https://{{ $tenant->slug }}.{{ config('app.root_domain') }}" target="_blank"
       class="text-sm text-orange-500 hover:underline">
        {{ $tenant->slug }}.{{ config('app.root_domain') }}
    </a>
</div>

{{-- Alerts --}}
@if($tenant->status === 'pending')
<div class="mb-5 flex items-start gap-3 rounded-xl bg-amber-50 border border-amber-200 px-5 py-4">
    <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>
        <p class="text-sm font-semibold text-amber-800">Choose a plan to go live</p>
        <p class="text-sm text-amber-700 mt-0.5">Your restaurant is set up and ready. Pick a plan below to start taking orders.</p>
    </div>
</div>
@endif

@if($tenant->status === 'past_due')
<div class="mb-5 flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-5 py-4">
    <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div>
        <p class="text-sm font-semibold text-red-800">Payment failed</p>
        <p class="text-sm text-red-700 mt-0.5">Update your payment method in <a href="{{ route('portal.billing') }}" class="underline">Billing</a> to keep your restaurant live.</p>
    </div>
</div>
@endif

{{-- Stat cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">

    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Status</p>
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
            {{ $tenant->status === 'active'    ? 'bg-green-50 text-green-700' : '' }}
            {{ $tenant->status === 'pending'   ? 'bg-amber-50 text-amber-700' : '' }}
            {{ $tenant->status === 'past_due'  ? 'bg-red-50 text-red-700' : '' }}
            {{ $tenant->status === 'suspended' ? 'bg-gray-100 text-gray-500' : '' }}">
            {{ ucfirst(str_replace('_', ' ', $tenant->status)) }}
        </span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Plan</p>
        @if($tenant->plan)
        <p class="text-sm font-semibold text-gray-900">{{ $tenant->plan->name }}</p>
        <p class="text-xs text-gray-400 mt-0.5">{{ $tenant->plan->price_monthly }}/mo</p>
        @else
        <p class="text-sm text-gray-400">No plan</p>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-2">Orders this month</p>
        @if($tenant->plan)
            <div class="flex items-baseline gap-1">
                <span class="text-xl font-bold text-gray-900">{{ number_format($ordersThisMonth) }}</span>
                @if($orderLimit)
                <span class="text-xs text-gray-400">/ {{ number_format($orderLimit) }}</span>
                @else
                <span class="text-xs text-gray-400">orders</span>
                @endif
            </div>
            @if($orderLimit)
            <div class="mt-2 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $usagePct >= 100 ? 'bg-red-500' : ($usagePct >= 80 ? 'bg-amber-400' : 'bg-orange-400') }}"
                     style="width: {{ $usagePct }}%"></div>
            </div>
            @if($usagePct >= 80)
            <p class="mt-1 text-xs {{ $usagePct >= 100 ? 'text-red-500' : 'text-amber-500' }}">
                {{ $usagePct >= 100 ? 'Over limit — overage applies' : $usagePct . '% used' }}
            </p>
            @endif
            @else
            <p class="mt-0.5 text-xs text-gray-400">Unlimited</p>
            @endif
        @else
        <p class="text-sm text-gray-400">—</p>
        @endif
    </div>

</div>

{{-- Plan picker --}}
@if($tenant->status === 'pending' && $plans->count())
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Choose a plan</h2>
        <p class="text-xs text-gray-400 mt-0.5">You can change your plan anytime from Billing.</p>
    </div>
    <div class="divide-y divide-gray-100 sm:divide-y-0 sm:grid sm:grid-cols-3 sm:divide-x">
        @foreach($plans as $plan)
        <div class="px-6 py-5 flex flex-col">
            <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ $plan->name }}</p>
                <p class="text-2xl font-bold text-orange-500 mt-1">
                    {{ $plan->price_monthly }}<span class="text-sm font-normal text-gray-400">/mo</span>
                </p>
                <ul class="mt-3 space-y-1.5 text-xs text-gray-500">
                    <li class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @if($plan->order_limit) {{ number_format($plan->order_limit) }} orders/mo @else Unlimited orders @endif
                    </li>
                    <li class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        {{ $plan->locations_included }} location{{ $plan->locations_included > 1 ? 's' : '' }}
                    </li>
                    @if($plan->overage_fee_per_order_cents > 0)
                    <li class="flex items-center gap-1.5 text-gray-400">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                        {{ $plan->overage_fee_per_order_cents }}¢/order overage
                    </li>
                    @endif
                </ul>
            </div>
            <form method="POST" action="{{ route('portal.onboarding.plan', $plan) }}" class="mt-5">
                @csrf
                <button type="submit"
                    class="w-full rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
                    Choose {{ $plan->name }}
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Active: quick actions --}}
@if($tenant->isActive())
<div class="grid grid-cols-2 gap-4">

    <div class="bg-orange-500 rounded-xl px-5 py-5">
        <p class="text-sm font-semibold text-orange-100">Restaurant Admin</p>
        <p class="text-xs text-orange-200 mt-0.5 mb-4">Manage your menu, orders, hours, and more.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('portal.access', 'admin/menus') }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-white text-orange-600 hover:bg-orange-50 px-3 py-2 text-sm font-semibold transition-colors">
                Edit Menu
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="{{ route('portal.access', 'admin/orders') }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 hover:bg-orange-700 text-white border border-orange-400 px-3 py-2 text-sm font-semibold transition-colors">
                Orders
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="{{ route('portal.access', 'admin/dashboard') }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 hover:bg-orange-700 text-white border border-orange-400 px-3 py-2 text-sm font-semibold transition-colors">
                Full Admin
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 px-5 py-5">
        <p class="text-sm font-semibold text-gray-700">Ordering link</p>
        <p class="text-xs text-gray-400 mt-0.5 mb-3">Share with customers or embed on your website.</p>
        @php $url = 'https://' . $tenant->slug . '.' . config('app.root_domain'); @endphp
        <div class="flex items-center gap-2">
            <code class="flex-1 min-w-0 truncate bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 font-mono">{{ $url }}</code>
            <button onclick="navigator.clipboard.writeText('{{ $url }}')"
                class="shrink-0 rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-500 hover:bg-gray-50 transition-colors">
                Copy
            </button>
        </div>
        <a href="{{ route('portal.embed') }}" class="mt-2 inline-block text-xs text-orange-500 hover:underline">All embed options →</a>
    </div>

</div>
@endif

@endsection
