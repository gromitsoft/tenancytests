<?php

namespace GromIT\TenancyTests\Tests\Tasks\SwitchTenant;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\Tasks\CreateTenant\CreateDatabase;
use GromIT\Tenancy\Tasks\DeleteTenant\DeleteDatabase;
use GromIT\Tenancy\Tasks\SwitchTenant\ChangeTenantDatabaseConnection;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class ChangeTenantDatabaseConnectionTest extends TenancyPluginTestCase
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

        (new CreateDatabase())->handle($this->tenant);
    }

    /**
     * @throws \GromIT\Tenancy\Exceptions\ChangeTenantDatabaseException
     */
    public function testChangesResourcesConfig(): void
    {
        $connectionName = $this->getTenantConnectionName();

        self::assertNull(config("database.connections.$connectionName.database"));

        $task = new ChangeTenantDatabaseConnection();
        $task->makeCurrent($this->tenant);

        self::assertSame(config("database.connections.$connectionName.database"), $this->tenant->database_name);

        $task->forgetCurrent();

        self::assertNull(config("database.connections.$connectionName.database"));
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        (new DeleteDatabase())->handle($this->tenant);

        Event::fakeFor(function () {
            $this->tenant->delete();
        });

        parent::tearDown();
    }
}
