<?php

namespace GromIT\TenancyTests\Tests\Tasks\SwitchTenant;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\Tasks\SwitchTenant\ChangeStorageDisk;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class ChangeStorageDiskTest extends TenancyPluginTestCase
{
    use CreatesTenants;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $tenant;

    /**
     * @var string
     */
    private $diskName;

    public function setUp(): void
    {
        parent::setUp();

        Event::fakeFor(function () {
            $this->tenant = $this->createTenant();
        });

        $this->diskName = $this->getTenantDiskName();
    }

    public function testStorageDiskConfig(): void
    {
        $defaultStorageConfig = [
            'root' => config("filesystems.disks.{$this->diskName}.root"),
            'url'  => config("filesystems.disks.{$this->diskName}.url")
        ];

        $task = new ChangeStorageDisk();
        $task->makeCurrent($this->tenant);

        $changedConfig = [
            'root' => config("filesystems.disks.{$this->diskName}.root"),
            'url'  => config("filesystems.disks.{$this->diskName}.url")
        ];

        self::assertNotEquals($defaultStorageConfig, $changedConfig);

        $task->forgetCurrent();

        $restoredConfig = [
            'root' => config("filesystems.disks.{$this->diskName}.root"),
            'url'  => config("filesystems.disks.{$this->diskName}.url")
        ];

        self::assertEquals($defaultStorageConfig, $restoredConfig);
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
