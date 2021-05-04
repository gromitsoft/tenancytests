<?php

namespace GromIT\TenancyTests\Tests\Tasks\CreateTenant;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\DatabaseCreators\MysqlDatabaseCreator;
use GromIT\Tenancy\Tasks\CreateTenant\CreateDatabase;
use GromIT\Tenancy\Tasks\DeleteTenant\DeleteDatabase;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class CreateDatabaseTest extends TenancyPluginTestCase
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
    }

    public function testCreatesDatabase(): void
    {
        $createTask = new CreateDatabase();
        $createTask->handle($this->tenant);

        self::assertNotEmpty($this->tenant->database_name);

        $dbCreator = new MysqlDatabaseCreator();

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
