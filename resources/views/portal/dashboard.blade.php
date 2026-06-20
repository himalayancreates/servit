@extends('portal.layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
    <p class="text-sm text-gray-500 mt-1">
        <a href="https://{{ $tenant->slug }}.{{ config('app.root_domain') }}" target="_blank"
           class="text-orange-500 hover:underline">
            {{ $tenant->slug }}.{{ config('app.root_domain') }}
        </a>
    </p>
</div>

{{-- Status cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    {{-- Status --}}
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Status</p>
        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
            {{ $tenant->status === 'active'    ? 'bg-green-50 text-green-700' : '' }}
            {{ $tenant->status === 'trialing'  ? 'bg-blue-50 text-blue-700' : '' }}
            {{ $tenant->status === 'past_due'  ? 'bg-red-50 text-red-700' : '' }}
            {{ $tenant->status === 'suspended' ? 'bg-gray-100 text-gray-600' : '' }}
        ">
            {{ ucfirst(str_replace('_', ' ', $tenant->status)) }}
        </span>

        @if($tenant->isTrialing() && $tenant->trial_ends_at)
        <p class="mt-2 text-xs text-gray-500">
            Trial ends {{ $tenant->trial_ends_at->diffForHumans() }}
        </p>
        @endif
    </div>

    {{-- Plan --}}
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Plan</p>
        @if($tenant->plan)
        <p class="text-lg font-bold text-gray-900">{{ $tenant->plan->name }}</p>
        <p class="text-sm text-gray-500 mt-0.5">{{ $tenant->plan->price_monthly }} / month</p>
        @else
        <p class="text-sm text-gray-400">No plan selected</p>
        @endif
    </div>

    {{-- Quick action --}}
    <div class="bg-orange-500 rounded-xl px-6 py-5 flex flex-col justify-between">
        <p class="text-xs font-semibold text-orange-100 uppercase tracking-wide mb-2">Restaurant Admin</p>
        <div>
            <p class="text-sm text-orange-100 mb-3">Manage your menu, orders, settings and more.</p>
            <a href="https://{{ $tenant->slug }}.{{ config('app.root_domain') }}/admin"
               target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg bg-white text-orange-600 px-4 py-2 text-sm font-semibold hover:bg-orange-50 transition-colors">
                Open Admin Panel
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>
</div>

{{-- Embed snippet --}}
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Your ordering link</h2>
        <p class="text-sm text-gray-500 mt-0.5">Share this link with your customers or embed it on your website.</p>
    </div>
    <div class="px-6 py-5 space-y-4">
        {{-- Direct link --}}
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Direct link</p>
            <div class="flex items-center gap-2">
                <code class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-700 font-mono break-all">
                    https://{{ $tenant->slug }}.{{ config('app.root_domain') }}/order/{{ $tenant->slug }}
                </code>
                <button onclick="navigator.clipboard.writeText('https://{{ $tenant->slug }}.{{ config('app.root_domain') }}/order/{{ $tenant->slug }}')"
                    class="shrink-0 rounded-lg border border-gray-200 px-3 py-2.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    Copy
                </button>
            </div>
        </div>

        {{-- Embed button --}}
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Embed on your website</p>
            <div class="flex items-start gap-2">
                <code class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-700 font-mono break-all whitespace-pre-wrap"><script src="https://{{ config('app.root_domain') }}/widget.js"></script>
<button data-servit-restaurant="{{ $tenant->slug }}">Order Online</button></code>
                <button onclick="navigator.clipboard.writeText('<script src=\'https://{{ config('app.root_domain') }}/widget.js\'><\/script>\n<button data-servit-restaurant=\'{{ $tenant->slug }}\'>Order Online<\/button>')"
                    class="shrink-0 rounded-lg border border-gray-200 px-3 py-2.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    Copy
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Available plans (if trialing and no plan) --}}
@if($tenant->isTrialing() && !$tenant->plan && $plans->count())
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Choose a plan</h2>
        <p class="text-sm text-gray-500 mt-0.5">Your trial ends {{ $tenant->trial_ends_at?->diffForHumans() }}. Pick a plan to keep your restaurant running.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
        @foreach($plans as $plan)
        <div class="px-6 py-5">
            <p class="font-bold text-gray-900 text-lg">{{ $plan->name }}</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">{{ $plan->price_monthly }}<span class="text-sm font-normal text-gray-500">/mo</span></p>
            <ul class="mt-3 space-y-1 text-sm text-gray-600">
                @if($plan->order_limit)
                <li>Up to {{ number_format($plan->order_limit) }} orders/month</li>
                @else
                <li>Unlimited orders</li>
                @endif
                <li>{{ $plan->locations_included }} location{{ $plan->locations_included > 1 ? 's' : '' }} included</li>
            </ul>
            <button class="mt-4 w-full rounded-lg border border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white px-4 py-2 text-sm font-semibold transition-colors">
                Choose {{ $plan->name }}
            </button>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
