<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal') — ServIt</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Top nav -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('portal.home') }}" class="text-lg font-bold text-orange-500 tracking-tight">ServIt</a>
                        <span class="text-xs text-gray-400 font-medium uppercase tracking-wide">Client Portal</span>
                    </div>

                    <div class="flex items-center gap-4">
                        @if($tenant ?? null)
                        <a href="https://{{ $tenant->slug }}.{{ config('app.root_domain') }}/admin"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2 text-sm font-semibold text-white transition-colors">
                            Open Restaurant Admin
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        @endif

                        <div class="relative group">
                            <button class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                                <div class="w-7 h-7 rounded-full bg-orange-100 flex items-center justify-center text-xs font-bold text-orange-600">
                                    {{ strtoupper(substr(app('auth')->guard('client')->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="hidden sm:block">{{ app('auth')->guard('client')->user()->name ?? '' }}</span>
                            </button>
                            <div class="absolute right-0 top-full mt-1 w-40 rounded-xl bg-white border border-gray-200 shadow-lg py-1 hidden group-hover:block z-50">
                                <form method="POST" action="{{ route('portal.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page content -->
        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
