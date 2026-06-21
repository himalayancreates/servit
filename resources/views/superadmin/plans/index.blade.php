@extends('superadmin.layouts.app')

@section('title', 'Plans')

@section('content')

@if(session('error'))
<div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
    {{ session('error') }}
</div>
@endif

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Plans</h1>
        <p class="text-sm text-gray-500 mt-1">Subscription tiers available to clients</p>
    </div>
    <a href="{{ route('dashboard.plans.create') }}"
       class="rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
        New plan
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Order limit</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Locations</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Platform fee</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Overage</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clients</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($plans as $plan)
            <tr class="hover:bg-gray-50 {{ !$plan->is_active ? 'opacity-60' : '' }}">
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900">{{ $plan->name }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $plan->slug }}</p>
                </td>
                <td class="px-6 py-4 font-semibold text-gray-900">{{ $plan->price_monthly }}/mo</td>
                <td class="px-6 py-4 text-gray-600">
                    {{ $plan->order_limit ? number_format($plan->order_limit) : 'Unlimited' }}
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $plan->locations_included }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $plan->platform_fee_percent }}%</td>
                <td class="px-6 py-4 text-gray-600">
                    ${{ number_format($plan->overage_fee_per_order_cents / 100, 2) }}/order
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $plan->tenants_count }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $plan->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard.plans.edit', $plan) }}"
                           class="text-xs font-medium text-orange-500 hover:text-orange-600">Edit</a>
                        @if($plan->tenants_count === 0)
                        <form method="POST" action="{{ route('dashboard.plans.destroy', $plan) }}"
                              onsubmit="return confirm('Delete {{ $plan->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-500">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-400">
                    No plans yet. <a href="{{ route('dashboard.plans.create') }}" class="text-orange-500 hover:underline">Create your first plan.</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
