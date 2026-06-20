<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — ServIt Portal</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full flex items-center justify-center">
    <div class="w-full max-w-sm px-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">ServIt</h1>
            <p class="mt-1 text-sm text-gray-400">Restaurant Portal</p>
        </div>

        <form method="POST" action="{{ route('portal.login') }}" class="space-y-4">
            @csrf

            @if($errors->any())
                <div class="rounded-lg bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required
                    value="{{ old('email') }}"
                    class="w-full rounded-lg bg-gray-800 border border-gray-700 px-4 py-2.5 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    placeholder="you@restaurant.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="w-full rounded-lg bg-gray-800 border border-gray-700 px-4 py-2.5 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-700 bg-gray-800 text-orange-500">
                    Remember me
                </label>
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors">
                Sign in
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-500">
            Don't have an account? You'll receive an invite from your ServIt representative.
        </p>
    </div>
</body>
</html>
