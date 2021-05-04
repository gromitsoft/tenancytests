<?php

namespace GromIT\TenancyTests\Tests\Actions\CurrentTenant;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\Actions\CurrentTenant\ForgetCurrentTenant;
use GromIT\Tenancy\Actions\CurrentTenant\MakeTenantCurrent;
use GromIT\Tenancy\Classes\TenancyManager;
use GromIT\Tenancy\Events\AfterForgetCurrentTenant;
use GromIT\Tenancy\Events\BeforeForgetCurrentTenant;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class ForgetCurrentTenantTest extends TenancyPluginTestCase
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

        MakeTenantCurrent::make()->execute($this->tenant);
    }

    /**
     * @throws \Exception
     */
    public function testForgetsCurrentTenant(): void
    {
        $this->expectsEvents(BeforeForgetCurrentTenant::class);
        $this->expectsEvents(AfterForgetCurrentTenant::class);

        ForgetCurrentTenant::make()->execute();

        self::assertEmpty(TenancyManager::instance()->getCurrent());
        self::assertFalse(TenancyManager::instance()->hasCurrent());
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
