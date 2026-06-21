<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal') — ServIt</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-gray-50">

<div class="flex h-full">

    {{-- ── Sidebar ─────────────────────────────────────────── --}}
    <aside style="width:220px;min-width:220px" class="bg-white border-r border-gray-200 flex flex-col">

        {{-- Brand --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <a href="{{ route('portal.home') }}" class="text-lg font-bold text-orange-500 tracking-tight">ServIt</a>
            @if(isset($tenant) && $tenant)
            <p class="mt-0.5 text-xs text-gray-400 truncate">{{ $tenant->name }}</p>
            @endif
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">

            @php
                $isActive = fn($name) => request()->routeIs($name);
            @endphp

            {{-- Dashboard --}}
            <a href="{{ route('portal.home') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $isActive('portal.home') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            {{-- Billing --}}
            <a href="{{ route('portal.billing') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $isActive('portal.billing') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Billing
            </a>

            {{-- Add-ons --}}
            <a href="{{ route('portal.addons') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $isActive('portal.addons') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                Add-ons
            </a>

            {{-- Embed --}}
            <a href="{{ route('portal.embed') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $isActive('portal.embed') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                Embed
            </a>

            {{-- Settings --}}
            <a href="{{ route('portal.settings') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $isActive('portal.settings') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>

        </nav>

        {{-- Bottom: admin link + user --}}
        <div class="px-3 pb-4 pt-3 border-t border-gray-100 space-y-0.5">

            @if(isset($tenant) && $tenant)
            <a href="{{ route('portal.access', 'admin/dashboard') }}" target="_blank"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-orange-600 hover:bg-orange-50 transition-colors">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Restaurant Admin
            </a>
            @endif

            <div class="flex items-center gap-2.5 px-3 py-2">
                <div style="width:24px;height:24px;flex-shrink:0" class="rounded-full bg-orange-100 flex items-center justify-center text-xs font-bold text-orange-600">
                    {{ strtoupper(substr(app('auth')->guard('client')->user()->name ?? 'U', 0, 1)) }}
                </div>
                <span class="text-xs text-gray-500 truncate flex-1 min-w-0">
                    {{ app('auth')->guard('client')->user()->name ?? '' }}
                </span>
                <form method="POST" action="{{ route('portal.logout') }}">
                    @csrf
                    <button type="submit" title="Sign out" class="text-gray-300 hover:text-gray-500 transition-colors">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>

        </div>
    </aside>

    {{-- ── Main ────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto">
        <main class="max-w-4xl mx-auto px-8 py-8">

            @if(session('success'))
            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
            @endif

            @yield('content')

        </main>
    </div>

</div>
</body>
</html>
