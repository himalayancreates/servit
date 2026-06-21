<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantPlan
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->bound('current.tenant')) {
            return $next($request);
        }

        /** @var Tenant $tenant */
        $tenant = app('current.tenant');

        if (! $tenant->plan_id) {
            return $next($request);
        }

        $key   = "tenant:{$tenant->id}:orders:" . now()->format('Y-m');
        $count = (int) Cache::get($key, 0);
        $limit = $tenant->plan?->order_limit;

        if ($limit && $count >= $limit) {
            $overageKey = "tenant:{$tenant->id}:overage:" . now()->format('Y-m');
            $ttl        = now()->endOfMonth()->diffInSeconds(now());
            Cache::add($overageKey, 0, $ttl);
            Cache::increment($overageKey);
        }

        return $next($request);
    }
}
