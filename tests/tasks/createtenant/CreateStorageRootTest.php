<?php

namespace GromIT\TenancyTests\Tests\Tasks\CreateTenant;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\Tasks\CreateTenant\CreateStorageRoot;
use GromIT\Tenancy\Tasks\DeleteTenant\DeleteStorageRoot;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class CreateStorageRootTest extends TenancyPluginTestCase
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

    public function testCreatesStorageRoot(): void
    {
        $createTask = new CreateStorageRoot();
        $createTask->handle($this->tenant);

        $rootPath = config("filesystems.disks.{$this->getTenantDiskName()}.root");
        $rootPath = str_replace('{tenant_id}', $this->tenant->id, $rootPath);

        self::assertDirectoryExists($rootPath);

        $deleteTask = new DeleteStorageRoot();
        $deleteTask->handle($this->tenant);

        self::assertDirectoryNotExists($rootPath);
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
