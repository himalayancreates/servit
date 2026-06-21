<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Models\User as TenantAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class HandleServitToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($token = $request->query('servit_token')) {
            $this->loginSuperAdmin($token);
        } elseif ($token = $request->query('servit_client_token')) {
            $this->loginClient($token);
        }

        return $next($request);
    }

    private function loginSuperAdmin(string $token): void
    {
        $tenantId = Cache::get("servit_access:{$token}");
        if (! $tenantId) {
            return;
        }

        Cache::forget("servit_access:{$token}");

        $admin = TenantAdmin::where('super_user', 1)->where('status', 1)->first();
        if ($admin) {
            AdminAuth::login($admin, remember: true);
        }
    }

    private function loginClient(string $token): void
    {
        $payload = Cache::get("servit_client_access:{$token}");
        if (! $payload) {
            return;
        }

        Cache::forget("servit_client_access:{$token}");

        $admin = TenantAdmin::where('email', $payload['email'])->where('status', 1)->first();
        if ($admin) {
            AdminAuth::login($admin, remember: true);
        }
    }
}
