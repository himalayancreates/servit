@extends('superadmin.layouts.app')

@section('title', 'Invitations')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Invitations</h1>
        <p class="text-sm text-gray-500 mt-1">All invites sent to prospective clients</p>
    </div>
    {{-- Quick invite from this page too --}}
    <form method="POST" action="{{ route('dashboard.tenants.invite') }}" class="flex gap-2">
        @csrf
        <input type="email" name="email" placeholder="restaurant@email.com" required
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 w-64">
        <button type="submit" class="rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
            Send invite
        </button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Sent</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expires / Accepted</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Invited by</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($invitations as $invitation)
            @php
                $status = $invitation->accepted_at
                    ? 'accepted'
                    : ($invitation->expires_at->isPast() ? 'expired' : 'pending');
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $invitation->email }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $status === 'accepted' ? 'bg-green-50 text-green-700' : '' }}
                        {{ $status === 'pending'  ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $status === 'expired'  ? 'bg-gray-100 text-gray-500'  : '' }}
                    ">{{ ucfirst($status) }}</span>
                </td>
                <td class="px-6 py-4 text-gray-500">{{ $invitation->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-gray-500">
                    @if($invitation->accepted_at)
                        {{ $invitation->accepted_at->format('M d, Y') }}
                    @else
                        {{ $invitation->expires_at->format('M d, Y') }}
                        @if($invitation->expires_at->isFuture())
                        <span class="text-xs text-gray-400">({{ $invitation->expires_at->diffForHumans() }})</span>
                        @endif
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-500">{{ $invitation->invitedBy?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-right">
                    @if(!$invitation->accepted_at)
                    <div class="flex items-center justify-end gap-3">
                        @if($invitation->expires_at->isPast() || $invitation->expires_at->diffInDays() < 2)
                        <form method="POST" action="{{ route('dashboard.invitations.resend', $invitation) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-orange-500 hover:text-orange-600">Resend</button>
                        </form>
                        @endif
                        @if($invitation->expires_at->isFuture())
                        <form method="POST" action="{{ route('dashboard.invitations.revoke', $invitation) }}"
                              onsubmit="return confirm('Revoke this invitation?')">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-500">Revoke</button>
                        </form>
                        @endif
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">No invitations sent yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($invitations->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $invitations->links() }}</div>
    @endif
</div>

@endsection
