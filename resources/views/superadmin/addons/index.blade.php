@extends('superadmin.layouts.app')

@section('title', 'Add-ons')

@section('content')

@if(session('error'))
<div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
@endif

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Add-ons</h1>
        <p class="text-sm text-gray-500 mt-1">Optional features clients can activate on top of their plan</p>
    </div>
    <a href="{{ route('dashboard.addons.create') }}"
       class="rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
        New add-on
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Add-on</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Billing</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Clients</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($addons as $addon)
            <tr class="hover:bg-gray-50 {{ !$addon->is_active ? 'opacity-60' : '' }}">
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-900">{{ $addon->name }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $addon->slug }}</p>
                    @if($addon->description)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $addon->description }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 font-semibold text-gray-900">{{ $addon->price }}/mo</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $addon->billing_type === 'flat' ? 'bg-gray-100 text-gray-600' : 'bg-blue-50 text-blue-700' }}">
                        {{ ucfirst($addon->billing_type) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $addon->tenants_count }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $addon->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $addon->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard.addons.edit', $addon) }}"
                           class="text-xs font-medium text-orange-500 hover:text-orange-600">Edit</a>
                        @if($addon->tenants_count === 0)
                        <form method="POST" action="{{ route('dashboard.addons.destroy', $addon) }}"
                              onsubmit="return confirm('Delete {{ $addon->name }}?')">
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
                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                    No add-ons yet. <a href="{{ route('dashboard.addons.create') }}" class="text-orange-500 hover:underline">Create your first add-on.</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
