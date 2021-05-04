<?php

namespace GromIT\TenancyTests\Tests;

use GromIT\Tenancy\Concerns\UsesTenancyConfig;
use GromIT\Tenancy\Models\Tenant;
use GromIT\Tenancy\Tasks\CreateTenant\CreateDatabase;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Support\Facades\Schema;

trait CreatesTenants
{
    use UsesTenancyConfig;

    protected function createTenant(): Tenant
    {
        return Tenant::create([
            'name'      => 'test tenant',
            'is_active' => true
        ]);
    }

    /**
     * @param \GromIT\Tenancy\Models\Tenant $tenant
     */
    protected function createDatabase(Tenant $tenant): void
    {
        $createDatabase = new CreateDatabase();
        $createDatabase->handle($tenant);
    }

    protected function createMigrationsTable(): void
    {
        Schema::connection($this->getTenantConnectionName())
            ->create(
                config('database.migrations'),
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('migration');
                    $table->integer('batch');
                }
            );
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function migrateBaseTables(): void
    {
        /** @var \Illuminate\Database\Migrations\Migrator $migrator */
        $migrator = app()->make('migrator');

        $migrations = config('gromit.tenancy::database.tenant_db_default_migrations');

        $defaultConnection = $this->getDefaultConnectionName();

        $migrator->setConnection($this->getTenantConnectionName());

        $migrator->run($migrations);

        $migrator->setConnection($defaultConnection);
    }
}
