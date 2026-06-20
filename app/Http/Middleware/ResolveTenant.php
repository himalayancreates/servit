<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $rootDomain = config('app.root_domain', 'servit.test');

        // Strip root domain to get subdomain (e.g. "pizza-palace" from "pizza-palace.servit.test")
        $subdomain = str_ends_with($host, '.' . $rootDomain)
            ? substr($host, 0, strlen($host) - strlen('.' . $rootDomain))
            : null;

        // No subdomain or reserved subdomains → ServIt platform routes
        if (! $subdomain || in_array($subdomain, ['www', 'app', 'cdn', 'api'])) {
            return $next($request);
        }

        // Resolve tenant from cache or DB (cache for 5 min to avoid per-request DB hit)
        $tenant = Cache::store('file')->remember("tenant:slug:{$subdomain}", 300, function () use ($subdomain) {
            return Tenant::on('servit')->where('slug', $subdomain)->first();
        });

        if (! $tenant) {
            abort(404, "Restaurant '{$subdomain}' not found.");
        }

        // Switch the tenant DB connection to this tenant's database
        Config::set('database.connections.tenant.database', $tenant->db_name);
        Config::set('database.connections.tenant.host', $tenant->db_host);
        DB::purge('tenant');

        // Make current tenant available to the rest of the request
        app()->instance('current.tenant', $tenant);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
