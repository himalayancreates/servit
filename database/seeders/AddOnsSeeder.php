<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddOnsSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            [
                'name'         => 'Custom Domain',
                'slug'         => 'custom_domain',
                'description'  => 'Use your own domain (e.g. orders.yourrestaurant.com) instead of yourname.servit.app',
                'billing_type' => 'flat',
                'price_cents'  => 1400,
            ],
            [
                'name'         => 'Custom SMTP',
                'slug'         => 'custom_smtp',
                'description'  => 'Send order confirmation emails from your own domain and email address',
                'billing_type' => 'flat',
                'price_cents'  => 900,
            ],
            [
                'name'         => 'Online Payments',
                'slug'         => 'online_payments',
                'description'  => 'Accept card payments online via Stripe. Connect your own Stripe account — payouts go directly to you',
                'billing_type' => 'flat',
                'price_cents'  => 0,
            ],
            [
                'name'         => 'Remove Branding',
                'slug'         => 'remove_branding',
                'description'  => 'Remove the "Powered by ServIt" footer from your ordering page',
                'billing_type' => 'flat',
                'price_cents'  => 900,
            ],
            [
                'name'         => 'Extra Location',
                'slug'         => 'extra_location',
                'description'  => 'Add an additional location beyond your plan\'s included limit',
                'billing_type' => 'metered',
                'price_cents'  => 1900,
            ],
            [
                'name'         => 'SMS Notifications',
                'slug'         => 'sms_notifications',
                'description'  => 'Receive SMS alerts for new orders and send order status updates to customers',
                'billing_type' => 'flat',
                'price_cents'  => 1200,
            ],
            [
                'name'         => 'QR Ordering',
                'slug'         => 'qr_ordering',
                'description'  => 'Generate QR codes for tables so dine-in customers can order from their phone',
                'billing_type' => 'flat',
                'price_cents'  => 900,
            ],
            [
                'name'         => 'Coupons & Discounts',
                'slug'         => 'coupons',
                'description'  => 'Create promo codes and automatic discounts for your customers',
                'billing_type' => 'flat',
                'price_cents'  => 900,
            ],
            [
                'name'         => 'Loyalty Program',
                'slug'         => 'loyalty_program',
                'description'  => 'Points-based rewards system to keep customers coming back',
                'billing_type' => 'flat',
                'price_cents'  => 1900,
            ],
            [
                'name'         => 'Advanced Analytics',
                'slug'         => 'advanced_analytics',
                'description'  => 'Revenue reports, peak hours, popular items, and customer insights',
                'billing_type' => 'flat',
                'price_cents'  => 1900,
            ],
            [
                'name'         => 'Priority Support',
                'slug'         => 'priority_support',
                'description'  => '4-hour response SLA with a dedicated support contact',
                'billing_type' => 'flat',
                'price_cents'  => 2900,
            ],
        ];

        foreach ($addons as $addon) {
            DB::connection('servit')->table('add_ons')->updateOrInsert(
                ['slug' => $addon['slug']],
                array_merge($addon, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
