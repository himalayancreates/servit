<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        foreach ($guards as $guard) {
            // Use app('auth') directly — TastyIgniter overrides Auth facade
            // which breaks Auth::guard() for our custom guards.
            if (app('auth')->guard($guard)->check()) {
                return redirect($this->redirectTo($request));
            }
        }

        return $next($request);
    }

    protected function redirectTo(Request $request): string
    {
        if ($request->is('portal*')) {
            return route('portal.home');
        }

        return route('dashboard.home');
    }
}
