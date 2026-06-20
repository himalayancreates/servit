<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'servit';

    public function up(): void
    {
        Schema::connection('servit')->create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->unsignedInteger('order_limit')->nullable(); // null = unlimited
            $table->unsignedInteger('rate_limit_per_hour')->nullable(); // null = unlimited
            $table->unsignedTinyInteger('locations_included')->default(1);
            $table->decimal('platform_fee_percent', 5, 2)->default(1.00);
            $table->unsignedInteger('price_monthly_cents');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::connection('servit')->create('add_ons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_product_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->enum('billing_type', ['flat', 'metered'])->default('flat');
            $table->unsignedInteger('price_cents');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::connection('servit')->create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token', 64)->unique();
            $table->foreignId('invited_by')->constrained('servit_admins')->cascadeOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        Schema::connection('servit')->create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('status', ['trialing', 'active', 'past_due', 'suspended', 'cancelled'])->default('trialing');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->foreignId('invitation_id')->nullable()->constrained('invitations')->nullOnDelete();
            $table->string('db_name')->unique(); // tenant_{id}
            $table->string('db_host')->default('127.0.0.1');
            $table->string('stripe_customer_id')->nullable()->unique();       // ServIt subscription billing
            $table->string('stripe_connect_account_id')->nullable()->unique(); // Restaurant's own Stripe
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('servit')->create('tenant_add_ons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('add_on_id')->constrained('add_ons')->cascadeOnDelete();
            $table->string('stripe_subscription_item_id')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamp('activated_at');
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'add_on_id']);
        });

        Schema::connection('servit')->create('tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->timestamp('ssl_provisioned_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('servit')->create('tenant_monthly_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('order_count')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::connection('servit')->dropIfExists('tenant_monthly_usage');
        Schema::connection('servit')->dropIfExists('tenant_domains');
        Schema::connection('servit')->dropIfExists('tenant_add_ons');
        Schema::connection('servit')->dropIfExists('tenants');
        Schema::connection('servit')->dropIfExists('invitations');
        Schema::connection('servit')->dropIfExists('add_ons');
        Schema::connection('servit')->dropIfExists('plans');
    }
};
