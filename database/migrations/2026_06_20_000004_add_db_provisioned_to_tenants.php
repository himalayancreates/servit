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
            $table->boolean('db_provisioned')->default(false)->after('db_host');
        });
    }

    public function down(): void
    {
        Schema::connection('servit')->table('tenants', function (Blueprint $table) {
            $table->dropColumn('db_provisioned');
        });
    }
};
