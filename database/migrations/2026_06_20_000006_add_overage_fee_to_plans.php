<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'servit';

    public function up(): void
    {
        Schema::connection('servit')->table('plans', function (Blueprint $table) {
            $table->unsignedInteger('overage_fee_per_order_cents')->default(10)->after('price_monthly_cents');
        });
    }

    public function down(): void
    {
        Schema::connection('servit')->table('plans', function (Blueprint $table) {
            $table->dropColumn('overage_fee_per_order_cents');
        });
    }
};
