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

        // Cache plain array (not Eloquent model) to avoid deserialization class-not-found issues
        $tenantData = Cache::store('file')->remember("tenant:slug:{$subdomain}", 300, function () use ($subdomain) {
            $t = Tenant::on('servit')->where('slug', $subdomain)->first();
            return $t ? ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug, 'status' => $t->status, 'db_name' => $t->db_name, 'db_host' => $t->db_host] : null;
        });

        if (! $tenantData) {
            abort(404, "Restaurant '{$subdomain}' not found.");
        }

        $tenant = new Tenant($tenantData);
        $tenant->exists = true;

        // Switch the tenant DB connection to this tenant's database
        Config::set('database.connections.tenant.database', $tenantData['db_name']);
        Config::set('database.connections.tenant.host', $tenantData['db_host']);
        DB::purge('tenant');

        // Make 'tenant' the default so TastyIgniter queries hit the right DB.
        // ServIt models are explicitly bound to 'servit' connection and are unaffected.
        Config::set('database.default', 'tenant');

        // Make current tenant available to the rest of the request
        app()->instance('current.tenant', $tenant);
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
