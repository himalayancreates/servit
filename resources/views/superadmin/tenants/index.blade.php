@extends('superadmin.layouts.app')

@section('title', 'Clients')

@section('content')

@if(session('invite_link'))
<div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 px-4 py-4">
    <p class="text-sm font-semibold text-blue-800 mb-1">{{ session('success') }}</p>
    <p class="text-xs text-blue-700 mb-2">Share this invite link (expires in 7 days):</p>
    <div class="flex items-center gap-2">
        <code class="flex-1 bg-white border border-blue-200 rounded px-3 py-1.5 text-xs text-blue-900 font-mono break-all">{{ session('invite_link') }}</code>
        <button onclick="navigator.clipboard.writeText('{{ session('invite_link') }}')"
            class="shrink-0 rounded bg-blue-600 text-white px-3 py-1.5 text-xs font-medium hover:bg-blue-700">Copy</button>
    </div>
</div>
@endif

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $tenants->total() }} total</p>
    </div>
    <form method="POST" action="{{ route('dashboard.tenants.invite') }}" class="flex gap-2">
        @csrf
        <input type="email" name="email" placeholder="restaurant@email.com" required
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 w-60">
        <button type="submit" class="rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors whitespace-nowrap">
            Send invite
        </button>
    </form>
</div>

{{-- Search & filters --}}
<form method="GET" action="{{ route('dashboard.tenants.index') }}" class="mb-4 flex items-center gap-3">
    <div class="relative flex-1 max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by name or slug…"
            class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
    </div>

    <select name="status" onchange="this.form.submit()"
        class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
        <option value="">All statuses</option>
        @foreach(['active','pending','past_due','suspended','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
            {{ ucfirst(str_replace('_', ' ', $s)) }}
        </option>
        @endforeach
    </select>

    <select name="plan_id" onchange="this.form.submit()"
        class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500">
        <option value="">All plans</option>
        @foreach($plans as $plan)
        <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
        @endforeach
    </select>

    <button type="submit" class="rounded-lg bg-gray-100 hover:bg-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition-colors">Search</button>

    @if(request()->hasAny(['search','status','plan_id']))
    <a href="{{ route('dashboard.tenants.index') }}" class="text-sm text-gray-400 hover:text-gray-600">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">DB</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($tenants as $tenant)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <a href="{{ route('dashboard.tenants.show', $tenant) }}"
                       class="font-medium text-gray-900 hover:text-orange-500">{{ $tenant->name }}</a>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $tenant->slug }}.{{ config('app.root_domain') }}</p>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $tenant->plan?->name ?? '—' }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $tenant->status === 'active'    ? 'bg-green-50 text-green-700' : '' }}
                        {{ $tenant->status === 'pending'   ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $tenant->status === 'past_due'  ? 'bg-red-50 text-red-700' : '' }}
                        {{ $tenant->status === 'suspended' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $tenant->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : '' }}
                    ">{{ ucfirst(str_replace('_', ' ', $tenant->status)) }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($tenant->db_provisioned)
                        <span class="inline-flex items-center gap-1 text-xs text-green-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Ready
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs text-amber-600">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Pending
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-xs text-gray-400">{{ $tenant->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('dashboard.tenants.show', $tenant) }}"
                           class="rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                            Edit
                        </a>
                        <a href="{{ route('dashboard.tenants.access', $tenant) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-600 px-3 py-1.5 text-xs font-semibold transition-colors">
                            Admin
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                    @if(request()->hasAny(['search','status','plan_id']))
                        No clients match your filters. <a href="{{ route('dashboard.tenants.index') }}" class="text-orange-500 hover:underline">Clear filters</a>
                    @else
                        No clients yet. Send an invite to get started.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($tenants->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $tenants->links() }}</div>
    @endif
</div>

@endsection
