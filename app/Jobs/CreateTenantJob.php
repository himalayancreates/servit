<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ClientUser;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 1;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly ClientUser $clientUser,
    ) {}

    public function handle(): void
    {
        $dbName = $this->tenant->db_name;
        $dbHost = $this->tenant->db_host;

        // 1. Create the database on the platform DB server
        DB::connection('servit')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        // 2. Point the tenant connection at the new database
        Config::set('database.connections.tenant.database', $dbName);
        Config::set('database.connections.tenant.host', $dbHost);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // 3. Create the migrations tracking table on the tenant DB
        Artisan::call('migrate:install', ['--database' => 'tenant']);

        // 4. Pre-mark ServIt-only migrations as "done" so the migrator skips them.
        //    These migrations use `protected $connection = 'servit'` and must never
        //    run against a tenant DB — they only belong in the platform schema.
        DB::connection('tenant')->table('migrations')->insert(
            collect(glob(database_path('migrations/2026_*.php')))
                ->map(fn($path) => ['migration' => basename($path, '.php'), 'batch' => 0])
                ->all()
        );

        // 5. Run TastyIgniter migrations and seeders against the tenant connection
        Artisan::call('igniter:up', [
            '--database' => 'tenant',
            '--force'    => true,
        ]);

        // 4. Create a TastyIgniter super-admin user for this tenant
        $this->createAdminUser($dbName);

        // 5. Mark tenant as provisioned
        $this->tenant->update(['db_provisioned' => true]);
    }

    private function createAdminUser(string $dbName): void
    {
        $username = Str::slug($this->clientUser->name, '_') ?: 'admin';

        // Ensure username is unique within this tenant DB
        $base = $username;
        $i = 2;
        while (DB::connection('tenant')->table('admin_users')->where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }

        DB::connection('tenant')->table('admin_users')->insert([
            'name'         => $this->clientUser->name,
            'email'        => $this->clientUser->email,
            'username'     => $username,
            'password'     => Hash::make(Str::random(32)), // random — portal "Open Admin" uses token impersonation
            'super_user'   => 1,
            'status'       => 1,
            'is_activated' => 1,
            'activated_at' => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
