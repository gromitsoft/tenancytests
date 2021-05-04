<?php

namespace GromIT\TenancyTests\Tests\Tasks\CreateTenant;

use GromIT\Tenancy\Actions\CurrentTenant\ForgetCurrentTenant;
use GromIT\Tenancy\Actions\CurrentTenant\MakeTenantCurrent;
use GromIT\Tenancy\Tasks\CreateTenant\CreateDatabase;
use GromIT\Tenancy\Tasks\CreateTenant\Migrate;
use GromIT\Tenancy\Tasks\DeleteTenant\DeleteDatabase;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;
use Illuminate\Support\Facades\Event;

class MigrateTest extends TenancyPluginTestCase
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

        (new CreateDatabase)->handle($this->tenant);
    }

    /**
     * @throws \October\Rain\Exception\SystemException
     */
    public function testMigrateBaseTablesAndPlugins(): void
    {
        $task = new Migrate();
        $task->handle($this->tenant);

        MakeTenantCurrent::make()->execute($this->tenant);

        $this->assertDatabaseHas(
            'migrations',
            ['migration' => '2013_10_01_000002_Db_System_Files'],
            $this->getTenantConnectionName()
        );

        $this->assertDatabaseHas(
            'gromit_tenancytests_posts',
            ['title' => 'seeded title'],
            $this->getTenantConnectionName()
        );

        ForgetCurrentTenant::make()->execute();
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        $deleteDatabase = new DeleteDatabase();
        $deleteDatabase->handle($this->tenant);

        Event::fakeFor(function () {
            $this->tenant->delete();
        });

        parent::tearDown();
    }
}
