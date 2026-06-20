@extends('superadmin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Platform overview</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-4 gap-5 mb-8">
    @foreach([
        ['label' => 'Total Clients',   'value' => $stats['total_tenants'],  'color' => 'text-gray-900'],
        ['label' => 'Active',          'value' => $stats['active_tenants'], 'color' => 'text-green-600'],
        ['label' => 'Trialing',        'value' => $stats['trialing'],       'color' => 'text-blue-600'],
        ['label' => 'Past Due',        'value' => $stats['past_due'],       'color' => 'text-red-600'],
    ] as $stat)
    <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
        <p class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
        <p class="mt-2 text-3xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-3 gap-6">
    <!-- Recent clients -->
    <div class="col-span-2 bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Recent Clients</h2>
            <a href="{{ route('superadmin.tenants.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentTenants as $tenant)
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $tenant->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $tenant->slug }}.servit.app</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $tenant->status === 'active' ? 'bg-green-50 text-green-700' : '' }}
                        {{ $tenant->status === 'trialing' ? 'bg-blue-50 text-blue-700' : '' }}
                        {{ $tenant->status === 'past_due' ? 'bg-red-50 text-red-700' : '' }}
                        {{ $tenant->status === 'suspended' ? 'bg-gray-100 text-gray-600' : '' }}
                    ">
                        {{ ucfirst($tenant->status) }}
                    </span>
                    <a href="{{ route('superadmin.tenants.access', $tenant) }}"
                       class="text-xs text-orange-500 hover:text-orange-600 font-medium">
                        Manage →
                    </a>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-sm text-gray-400">No clients yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Plans breakdown -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Plans</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($plans as $plan)
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">{{ $plan->name }}</p>
                    <span class="text-sm font-bold text-gray-900">{{ $plan->tenants_count }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">{{ $plan->price_monthly }} / mo</p>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-sm text-gray-400">No plans configured.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
