<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\TenantsMigrate::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(\App\Http\Middleware\ResolveTenant::class);

        // HandleServitToken must run AFTER StartSession (web group) so AdminAuth::login() can write the session.
        $middleware->web(append: \App\Http\Middleware\HandleServitToken::class);

        $middleware->alias([
            'auth'              => \App\Http\Middleware\Authenticate::class,
            'guest'             => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'tenant.plan'       => \App\Http\Middleware\CheckTenantPlan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // On subdomain (TI) requests, auth failures redirect to TI's own login — not our portals.
        // Without this, unauthenticated() falls back to route('login') which doesn't exist → crash.
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 401);
            }
            $rootDomain = config('app.root_domain', 'servit.test');
            $isSubdomain = str_ends_with($request->getHost(), '.' . $rootDomain);
            if ($isSubdomain) {
                return redirect($e->redirectTo($request) ?? admin_url('login'));
            }
            if ($request->is('portal*')) {
                return redirect()->guest(route('portal.login'));
            }
            return redirect()->guest(route('dashboard.login'));
        });
    })->create();
