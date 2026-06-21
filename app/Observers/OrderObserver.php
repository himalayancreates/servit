<?php

declare(strict_types=1);

namespace App\Observers;

use Igniter\Cart\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    public function created(Order $order): void
    {
        if (! app()->bound('current.tenant')) {
            return;
        }

        $tenant = app('current.tenant');
        $key    = "tenant:{$tenant->id}:orders:" . now()->format('Y-m');
        $ttl    = now()->endOfMonth()->diffInSeconds(now());

        Cache::add($key, 0, $ttl);
        Cache::increment($key);
    }
}
