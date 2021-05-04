<?php

namespace GromIT\TenancyTests\Tests\Tasks\SwitchTenant;

use GromIT\Tenancy\Tasks\SwitchTenant\ChangeTenantLoggingChannel;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;
use Illuminate\Support\Facades\Event;

class ChangeTenantLoggingChannelTest extends TenancyPluginTestCase
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

    public function testChangesLoggingConfig(): void
    {
        $defaultConfig = config('logging.channels.tenant');

        $task = new ChangeTenantLoggingChannel();
        $task->makeCurrent($this->tenant);

        self::assertNotEquals($defaultConfig, config('logging.channels.tenant'));
        self::assertStringContainsString("/{$this->tenant->id}/", config('logging.channels.tenant.path'));

        $task->forgetCurrent();

        self::assertEquals($defaultConfig, config('logging.channels.tenant'));
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
