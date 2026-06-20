<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'servit';

    public function up(): void
    {
        Schema::connection('servit')->table('tenants', function (Blueprint $table) {
            $table->dropColumn('trial_ends_at');
            $table->unsignedInteger('overage_fee_per_order_cents')->nullable()->after('stripe_connect_account_id');
            $table->text('notes')->nullable()->after('overage_fee_per_order_cents');
        });

        // Redefine status enum without 'trialing', add 'pending'
        DB::connection('servit')->statement(
            "ALTER TABLE tenants MODIFY status ENUM('pending','active','past_due','suspended','cancelled') NOT NULL DEFAULT 'pending'"
        );
    }

    public function down(): void
    {
        DB::connection('servit')->statement(
            "ALTER TABLE tenants MODIFY status ENUM('trialing','active','past_due','suspended','cancelled') NOT NULL DEFAULT 'trialing'"
        );

        Schema::connection('servit')->table('tenants', function (Blueprint $table) {
            $table->dropColumn(['overage_fee_per_order_cents', 'notes']);
            $table->timestamp('trial_ends_at')->nullable()->after('stripe_connect_account_id');
        });
    }
};
