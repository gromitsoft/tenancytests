<?php

namespace GromIT\TenancyTests\Tests\Tasks\SwitchTenant;

use GromIT\Tenancy\Tasks\SwitchTenant\ChangeResourcesStorage;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;
use Illuminate\Support\Facades\Event;

class ChangeResourcesStorageTest extends TenancyPluginTestCase
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

    public function testChangesResourcesConfig(): void
    {
        $defaultStorageConfig = config('system.storage');

        $task = new ChangeResourcesStorage();
        $task->makeCurrent($this->tenant);

        $changedConfig = config('system.storage');

        self::assertNotEquals($defaultStorageConfig, $changedConfig);

        self::assertEquals(config('system.storage.media.disk'), $this->getTenantDiskName());
        self::assertEquals(config('system.storage.resized.disk'), $this->getTenantDiskName());
        self::assertEquals(config('system.storage.uploads.disk'), $this->getTenantDiskName());

        self::assertStringContainsString("/{$this->tenant->id}/", config('system.storage.media.path'));
        self::assertStringContainsString("/{$this->tenant->id}/", config('system.storage.resized.path'));
        self::assertStringContainsString("/{$this->tenant->id}/", config('system.storage.uploads.path'));

        $task->forgetCurrent();

        $restoredConfig = config('system.storage');

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
