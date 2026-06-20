<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantsMigrate extends Command
{
    protected $signature = 'tenants:migrate {--tenant= : Only migrate a specific tenant ID}';
    protected $description = 'Run TastyIgniter migrations across all provisioned tenant databases';

    public function handle(): int
    {
        $query = Tenant::on('servit')->where('db_provisioned', true);

        if ($id = $this->option('tenant')) {
            $query->where('id', $id);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->warn('No provisioned tenants found.');
            return self::SUCCESS;
        }

        $this->info("Migrating {$tenants->count()} tenant(s)...");

        $failed = [];

        foreach ($tenants as $tenant) {
            $this->line("  → [{$tenant->id}] {$tenant->name} ({$tenant->db_name})");

            try {
                Config::set('database.connections.tenant.database', $tenant->db_name);
                Config::set('database.connections.tenant.host', $tenant->db_host);
                DB::purge('tenant');
                DB::reconnect('tenant');

                Artisan::call('igniter:up', [
                    '--database' => 'tenant',
                    '--force'    => true,
                ]);

                $this->line('    <info>done</info>');
            } catch (\Throwable $e) {
                $this->error("    failed: {$e->getMessage()}");
                $failed[] = $tenant->id;
            }
        }

        if ($failed) {
            $this->error('Failed tenant IDs: ' . implode(', ', $failed));
            return self::FAILURE;
        }

        $this->info('All tenants migrated.');
        return self::SUCCESS;
    }
}
