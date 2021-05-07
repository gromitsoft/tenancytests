<?php

namespace GromIT\TenancyTests\Tests\Tasks\CreateTenant;

use GromIT\Tenancy\Concerns\UsesTenancyConfig;
use GromIT\Tenancy\Tasks\CreateTenant\CreateDatabase;
use GromIT\Tenancy\Tasks\DeleteTenant\DeleteDatabase;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;
use Illuminate\Support\Facades\Event;

class CreateDatabaseTest extends TenancyPluginTestCase
{
    use CreatesTenants;
    use UsesTenancyConfig;

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
    }

    public function testCreatesDatabase(): void
    {
        $createTask = new CreateDatabase();
        $createTask->handle($this->tenant);

        self::assertNotEmpty($this->tenant->database_name);

        $dbCreator = $this->getDatabaseCreator();

        self::assertTrue($dbCreator->databaseExists($this->tenant->database_name));

        $deleteTask = new DeleteDatabase();
        $deleteTask->handle($this->tenant);

        self::assertFalse($dbCreator->databaseExists($this->tenant->database_name));
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        Event::fakeFor(function () {
            $this->tenant->delete();
        });

        parent::tearDown();
    }
}
