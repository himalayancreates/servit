@extends('superadmin.layouts.app')

@section('title', 'Clients')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
        <p class="text-sm text-gray-500 mt-1">All restaurant tenants</p>
    </div>

    <!-- Invite form -->
    <form method="POST" action="{{ route('superadmin.tenants.invite') }}" class="flex gap-2">
        @csrf
        <input
            type="email"
            name="email"
            placeholder="restaurant@email.com"
            required
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
        >
        <button type="submit" class="rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
            Send Invite
        </button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Trial ends</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Database</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($tenants as $tenant)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900">{{ $tenant->name }}</p>
                    <p class="text-xs text-gray-400">{{ $tenant->slug }}.servit.app</p>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $tenant->plan?->name ?? '—' }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $tenant->status === 'active'    ? 'bg-green-50 text-green-700' : '' }}
                        {{ $tenant->status === 'trialing'  ? 'bg-blue-50 text-blue-700' : '' }}
                        {{ $tenant->status === 'past_due'  ? 'bg-red-50 text-red-700' : '' }}
                        {{ $tenant->status === 'suspended' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $tenant->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : '' }}
                    ">
                        {{ ucfirst(str_replace('_', ' ', $tenant->status)) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500 text-xs">
                    {{ $tenant->trial_ends_at?->format('M d, Y') ?? '—' }}
                </td>
                <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $tenant->db_name }}</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('superadmin.tenants.access', $tenant) }}"
                       class="inline-flex items-center gap-1 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-600 px-3 py-1.5 text-xs font-semibold transition-colors">
                        Manage
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                    No clients yet. Send an invite to get started.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($tenants->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $tenants->links() }}
    </div>
    @endif
</div>
@endsection
