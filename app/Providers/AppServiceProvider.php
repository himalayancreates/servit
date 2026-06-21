<?php

namespace App\Providers;

use App\Observers\OrderObserver;
use Igniter\Cart\Models\Order;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Order::observe(OrderObserver::class);
    }
}
