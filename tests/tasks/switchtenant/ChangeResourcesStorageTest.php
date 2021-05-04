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
        $defaultStorageConfig = config('cms.storage');

        $task = new ChangeResourcesStorage();
        $task->makeCurrent($this->tenant);

        $changedConfig = config('cms.storage');

        self::assertNotEquals($defaultStorageConfig, $changedConfig);

        self::assertEquals(config('cms.storage.media.disk'), $this->getTenantDiskName());
        self::assertEquals(config('cms.storage.resized.disk'), $this->getTenantDiskName());
        self::assertEquals(config('cms.storage.uploads.disk'), $this->getTenantDiskName());

        self::assertStringContainsString("/{$this->tenant->id}/", config('cms.storage.media.path'));
        self::assertStringContainsString("/{$this->tenant->id}/", config('cms.storage.resized.path'));
        self::assertStringContainsString("/{$this->tenant->id}/", config('cms.storage.uploads.path'));

        $task->forgetCurrent();

        $restoredConfig = config('cms.storage');

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
