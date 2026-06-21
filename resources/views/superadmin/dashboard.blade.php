@extends('superadmin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, F j, Y') }}</p>
    </div>
    <form method="POST" action="{{ route('dashboard.tenants.invite') }}" class="flex gap-2">
        @csrf
        <input type="email" name="email" placeholder="restaurant@email.com" required
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 w-64">
        <button type="submit" class="rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors whitespace-nowrap">
            Send invite
        </button>
    </form>
</div>

{{-- Provisioning alerts --}}
@if($stuckTenants->count())
<div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-5 py-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-red-800">{{ $stuckTenants->count() }} tenant{{ $stuckTenants->count() > 1 ? 's' : '' }} stuck in provisioning</p>
            <p class="text-xs text-red-700 mt-0.5 mb-2">These databases haven't finished setting up after 10+ minutes. Open the client page and use the <strong>Re-run provisioning</strong> button.</p>
            <div class="flex flex-wrap gap-2">
                @foreach($stuckTenants as $stuck)
                <a href="{{ route('dashboard.tenants.show', $stuck) }}"
                   class="inline-flex items-center rounded-lg bg-white border border-red-200 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-50">
                    {{ $stuck->name }}
                    <span class="ml-1.5 text-red-400">· {{ $stuck->created_at->diffForHumans() }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Row 1: MRR + primary stats --}}
<div class="grid grid-cols-6 gap-4 mb-4">
    {{-- MRR hero card --}}
    <div class="col-span-2 bg-orange-500 rounded-xl px-6 py-5">
        <p class="text-sm font-medium text-orange-100">Monthly Recurring Revenue</p>
        <p class="mt-2 text-4xl font-bold text-white">{{ $stats['mrr'] }}</p>
        <div class="mt-2 flex items-center gap-3 text-xs text-orange-200">
            <span>Plans <span class="text-white font-semibold">{{ $stats['mrr_plans'] }}</span></span>
            <span class="text-orange-300">·</span>
            <span>Add-ons <span class="text-white font-semibold">{{ $stats['mrr_addons'] }}</span></span>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-5">
        <p class="text-xs font-medium text-gray-500">Total clients</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_tenants'] }}</p>
        <p class="mt-1 text-xs text-gray-400">all time</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-5">
        <p class="text-xs font-medium text-gray-500">Active</p>
        <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['active_tenants'] }}</p>
        <p class="mt-1 text-xs text-gray-400">on a plan</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-5">
        <p class="text-xs font-medium text-gray-500">New this month</p>
        <p class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['new_this_month'] }}</p>
        <p class="mt-1 text-xs text-gray-400">{{ now()->format('F') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-5">
        <p class="text-xs font-medium text-gray-500">Pending</p>
        <p class="mt-2 text-3xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        <p class="mt-1 text-xs text-gray-400">no plan yet</p>
    </div>
</div>

{{-- Row 2: secondary stats --}}
<div class="grid grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <p class="text-xs font-medium text-gray-500">Past due</p>
        <p class="mt-1.5 text-2xl font-bold text-red-600">{{ $stats['past_due'] }}</p>
        <p class="mt-1 text-xs text-gray-400">payment failed</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <p class="text-xs font-medium text-gray-500">Suspended</p>
        <p class="mt-1.5 text-2xl font-bold text-gray-500">{{ $stats['suspended'] }}</p>
        <p class="mt-1 text-xs text-gray-400">access blocked</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <p class="text-xs font-medium text-gray-500">Invite conversion</p>
        <p class="mt-1.5 text-2xl font-bold text-indigo-600">{{ $stats['conversion_rate'] }}%</p>
        <p class="mt-1 text-xs text-gray-400">{{ $stats['invites_accepted'] }} of {{ $stats['invites_sent'] }} accepted</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">
        <p class="text-xs font-medium text-gray-500">Add-on revenue share</p>
        @php
            $totalMrrCents = ($stats['mrr_plans'] !== '$0' || $stats['mrr_addons'] !== '$0')
                ? true : false;
            $planVal  = (int) str_replace(['$', ','], '', $stats['mrr_plans']);
            $addonVal = (int) str_replace(['$', ','], '', $stats['mrr_addons']);
            $total    = $planVal + $addonVal;
            $addonPct = $total > 0 ? round(($addonVal / $total) * 100) : 0;
        @endphp
        <p class="mt-1.5 text-2xl font-bold text-purple-600">{{ $addonPct }}%</p>
        <p class="mt-1 text-xs text-gray-400">of MRR from add-ons</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Recent clients --}}
    <div class="col-span-2 bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Recent clients</h2>
            <a href="{{ route('dashboard.tenants.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentTenants as $tenant)
            <div class="px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                    </div>
                    <div>
                        <a href="{{ route('dashboard.tenants.show', $tenant) }}"
                           class="text-sm font-medium text-gray-900 hover:text-orange-500">{{ $tenant->name }}</a>
                        <p class="text-xs text-gray-400">{{ $tenant->plan?->name ?? 'No plan' }} · {{ $tenant->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $tenant->status === 'active'    ? 'bg-green-50 text-green-700' : '' }}
                        {{ $tenant->status === 'pending'   ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $tenant->status === 'past_due'  ? 'bg-red-50 text-red-700' : '' }}
                        {{ $tenant->status === 'suspended' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $tenant->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : '' }}
                    ">{{ ucfirst(str_replace('_', ' ', $tenant->status)) }}</span>
                    <a href="{{ route('dashboard.tenants.access', $tenant) }}" target="_blank"
                       class="text-xs text-gray-400 hover:text-orange-500">Manage →</a>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-sm text-gray-400">No clients yet. Send your first invite above.</div>
            @endforelse
        </div>
    </div>

    {{-- Right column --}}
    <div class="space-y-6">

        {{-- Plans breakdown --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Plans</h2>
                <a href="{{ route('dashboard.plans.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">Manage →</a>
            </div>
            @forelse($plans as $plan)
            <div class="px-6 py-3 flex items-center justify-between border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $plan->name }}</p>
                    <p class="text-xs text-gray-400">{{ $plan->price_monthly }}/mo</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-800">{{ $plan->tenants_count }}</p>
                    <p class="text-xs text-gray-400">clients</p>
                </div>
            </div>
            @empty
            <div class="px-6 py-5 text-center text-sm text-gray-400">
                <a href="{{ route('dashboard.plans.create') }}" class="text-orange-500 hover:underline">Create your first plan →</a>
            </div>
            @endforelse
        </div>

        {{-- Top add-ons --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Top add-ons</h2>
                <a href="{{ route('dashboard.addons.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">Manage →</a>
            </div>
            @forelse($topAddons as $addon)
            @php $maxCount = $topAddons->first()->active_count ?: 1; @endphp
            <div class="px-6 py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center justify-between mb-1.5">
                    <p class="text-sm text-gray-800 truncate pr-4">{{ $addon->name }}</p>
                    <p class="text-xs font-semibold text-gray-600 shrink-0">{{ $addon->active_count }}</p>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-orange-400 h-1.5 rounded-full transition-all"
                         style="width: {{ $maxCount > 0 ? round(($addon->active_count / $maxCount) * 100) : 0 }}%"></div>
                </div>
            </div>
            @empty
            <div class="px-6 py-5 text-center text-sm text-gray-400">No add-ons activated yet.</div>
            @endforelse
        </div>

        {{-- Recent activity --}}
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Activity</h2>
                <a href="{{ route('dashboard.invitations.index') }}" class="text-sm text-orange-500 hover:text-orange-600 font-medium">Invites →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentActivity as $event)
                <div class="px-6 py-3 flex items-start gap-3">
                    <div class="mt-0.5 w-2 h-2 rounded-full shrink-0
                        {{ $event['type'] === 'signup'   ? 'bg-green-400' : '' }}
                        {{ $event['type'] === 'accepted' ? 'bg-blue-400'  : '' }}
                        {{ $event['type'] === 'invited'  ? 'bg-gray-300'  : '' }}
                    "></div>
                    <div>
                        <p class="text-sm text-gray-800">{{ $event['label'] }}</p>
                        <p class="text-xs text-gray-400">{{ $event['sub'] }}</p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-5 text-center text-sm text-gray-400">No activity yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Pending invites --}}
        @if($pendingInvites->count())
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Pending invites
                    <span class="ml-1.5 inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs font-semibold">{{ $pendingInvites->count() }}</span>
                </h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($pendingInvites as $invite)
                <div class="px-6 py-3">
                    <p class="text-sm text-gray-800 truncate">{{ $invite->email }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Expires {{ $invite->expires_at->diffForHumans() }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
