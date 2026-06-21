<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — ServIt Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 w-64 bg-gray-900 flex flex-col">
            <div class="flex h-16 items-center px-6 border-b border-gray-800">
                <span class="text-white font-bold text-xl tracking-tight">ServIt</span>
                <span class="ml-2 text-xs text-orange-400 font-medium uppercase tracking-widest">Admin</span>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-0.5 overflow-y-auto">
                <a href="{{ route('dashboard.home') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.home') ? 'bg-orange-500 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-4.5 h-4.5 w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <p class="px-3 pt-5 pb-1 text-xs font-semibold text-gray-600 uppercase tracking-widest">Clients</p>

                <a href="{{ route('dashboard.tenants.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.tenants.*') ? 'bg-orange-500 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    All clients
                </a>
                <a href="{{ route('dashboard.invitations.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.invitations.*') ? 'bg-orange-500 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Invitations
                </a>

                <p class="px-3 pt-5 pb-1 text-xs font-semibold text-gray-600 uppercase tracking-widest">Catalogue</p>

                <a href="{{ route('dashboard.plans.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.plans.*') ? 'bg-orange-500 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Plans
                </a>
                <a href="{{ route('dashboard.addons.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.addons.*') ? 'bg-orange-500 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Add-ons
                </a>

                <p class="px-3 pt-5 pb-1 text-xs font-semibold text-gray-600 uppercase tracking-widest">System</p>

                <a href="{{ route('dashboard.settings') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard.settings') ? 'bg-orange-500 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
            </nav>

            <div class="px-4 py-4 border-t border-gray-800">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white text-sm font-bold shrink-0">
                        {{ strtoupper(substr(auth('superadmin')->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth('superadmin')->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth('superadmin')->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('dashboard.logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:text-white rounded-lg hover:bg-gray-800 transition-colors">
                        Sign out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main content -->
        <div class="pl-64">
            <main class="p-8">
                @if(session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
