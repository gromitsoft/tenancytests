<?php

namespace GromIT\TenancyTests\Tests\Actions\CurrentTenant;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\Actions\CurrentTenant\MakeTenantCurrent;
use GromIT\Tenancy\Classes\TenancyManager;
use GromIT\Tenancy\Events\AfterMakeTenantCurrent;
use GromIT\Tenancy\Events\BeforeMakeTenantCurrent;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class MakeTenantCurrentTest extends TenancyPluginTestCase
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

    /**
     * @throws \Exception
     */
    public function testMakesTenantCurrent(): void
    {
        $this->expectsEvents(BeforeMakeTenantCurrent::class);
        $this->expectsEvents(AfterMakeTenantCurrent::class);

        MakeTenantCurrent::make()->execute($this->tenant);

        self::assertSame(
            TenancyManager::instance()->getCurrent()->id,
            $this->tenant->id
        );
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
