<?php

namespace GromIT\TenancyTests\Tests\Classes;

use GromIT\Tenancy\Actions\CurrentTenant\ForgetCurrentTenant;
use GromIT\Tenancy\Actions\CurrentTenant\MakeTenantCurrent;
use GromIT\Tenancy\Classes\PluginsUpdater;
use GromIT\Tenancy\Tasks\DeleteTenant\DeleteDatabase;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;
use Illuminate\Support\Facades\Event;
use RuntimeException;
use System\Classes\PluginManager;

class PluginsUpdaterTest extends TenancyPluginTestCase
{
    use CreatesTenants;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $tenant;

    public function setUp(): void
    {
        parent::setUp();

        Event::fakeFor(function () {
            $this->tenant = $this->createTenant();
        });

        $this->tenant->database_name = $this->getDefaultDatabaseName() . '_tenant_' . $this->tenant->id;
        $this->tenant->save();

        $this->createDatabase($this->tenant);

        config(["database.connections.{$this->getTenantConnectionName()}.database" => $this->tenant->database_name]);

        $this->createMigrationsTable();
        $this->migrateBaseTables();

        config(["database.connections.{$this->getTenantConnectionName()}.database" => null]);

        MakeTenantCurrent::make()->execute($this->tenant);
    }

    /**
     * @throws \October\Rain\Exception\SystemException
     */
    public function testUpdatesPlugins(): void
    {
        $this->updatePlugins();
    }

    /**
     * @throws \October\Rain\Exception\SystemException
     */
    public function testRefreshesPlugins(): void
    {
        $this->updatePlugins(true);
    }

    /**
     * @param bool $refresh
     *
     * @throws \October\Rain\Exception\SystemException
     */
    protected function updatePlugins(bool $refresh = false): void
    {
        $code   = 'GromIT.TenancyTests';
        $plugin = PluginManager::instance()->findByIdentifier($code);

        if ($plugin === null) {
            throw new RuntimeException("Plugin {$code} not found");
        }

        $pluginUpdater = PluginsUpdater::instance();
        $pluginUpdater->updatePluginsForTenants(
            collect([$this->tenant]),
            collect([$code => $plugin]),
            $refresh
        );

        $this->assertDatabaseHas(
            'system_plugin_versions',
            ['code' => $code],
            $this->getTenantConnectionName()
        );
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        ForgetCurrentTenant::make()->execute();

        $deleteDatabase = new DeleteDatabase();
        $deleteDatabase->handle($this->tenant);

        Event::fakeFor(function () {
            $this->tenant->delete();
        });

        parent::tearDown();
    }
}
