<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                        => 'Starter',
                'slug'                        => 'starter',
                'order_limit'                 => 500,
                'rate_limit_per_hour'         => 20,
                'locations_included'          => 1,
                'platform_fee_percent'        => 1.00,
                'price_monthly_cents'         => 2900,
                'overage_fee_per_order_cents' => 15,
                'is_active'                   => true,
            ],
            [
                'name'                        => 'Growth',
                'slug'                        => 'growth',
                'order_limit'                 => 2000,
                'rate_limit_per_hour'         => 100,
                'locations_included'          => 1,
                'platform_fee_percent'        => 0.50,
                'price_monthly_cents'         => 7900,
                'overage_fee_per_order_cents' => 10,
                'is_active'                   => true,
            ],
            [
                'name'                        => 'Pro',
                'slug'                        => 'pro',
                'order_limit'                 => null,
                'rate_limit_per_hour'         => null,
                'locations_included'          => 3,
                'platform_fee_percent'        => 0.30,
                'price_monthly_cents'         => 19900,
                'overage_fee_per_order_cents' => 0,
                'is_active'                   => true,
            ],
        ];

        foreach ($plans as $plan) {
            DB::connection('servit')->table('plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                array_merge($plan, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
