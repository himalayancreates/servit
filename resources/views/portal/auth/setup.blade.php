<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set up your account — ServIt</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full flex items-center justify-center py-12">
    <div class="w-full max-w-md px-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">ServIt</h1>
            <p class="mt-2 text-sm text-gray-400">You've been invited. Set up your account to get started.</p>
        </div>

        <div class="rounded-xl bg-gray-800/50 border border-gray-700 px-6 py-5 mb-6 text-sm text-gray-300">
            Signing up as <span class="text-white font-medium">{{ $invitation->email }}</span>
        </div>

        <form method="POST" action="{{ route('portal.invite', $token) }}" class="space-y-5">
            @csrf

            @if($errors->any())
                <div class="rounded-lg bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label for="restaurant_name" class="block text-sm font-medium text-gray-300 mb-1">Restaurant name</label>
                <input id="restaurant_name" name="restaurant_name" type="text" required
                    value="{{ old('restaurant_name') }}"
                    class="w-full rounded-lg bg-gray-800 border border-gray-700 px-4 py-2.5 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    placeholder="Pizza Palace">
                <p class="mt-1 text-xs text-gray-500">This becomes your ordering URL: <em>your-restaurant</em>.servit.app</p>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Your name</label>
                <input id="name" name="name" type="text" required
                    value="{{ old('name') }}"
                    class="w-full rounded-lg bg-gray-800 border border-gray-700 px-4 py-2.5 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    placeholder="Jane Smith">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input id="password" name="password" type="password" required
                    class="w-full rounded-lg bg-gray-800 border border-gray-700 px-4 py-2.5 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    placeholder="8 characters minimum">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="w-full rounded-lg bg-gray-800 border border-gray-700 px-4 py-2.5 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-orange-500 hover:bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors">
                Create account
            </button>
        </form>
    </div>
</body>
</html>
