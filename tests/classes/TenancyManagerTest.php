<?php

namespace GromIT\TenancyTests\Tests\Classes;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\Classes\TenancyManager;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class TenancyManagerTest extends TenancyPluginTestCase
{
    use CreatesTenants;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $tenant;

    /**
     * @var \GromIT\Tenancy\Classes\TenancyManager
     */
    private $tenancyManager;

    public function setUp(): void
    {
        parent::setUp();

        Event::fakeFor(function () {
            $this->tenant = $this->createTenant();
        });

        $this->tenancyManager = TenancyManager::instance();
    }

    public function testSetCurrent(): void
    {
        $this->tenancyManager->setCurrent($this->tenant);

        self::assertEquals($this->tenant->id, $this->tenancyManager->getCurrent()->id);
    }

    public function testForgetCurrent(): void
    {
        $this->tenancyManager->forgetCurrent();

        self::assertEmpty($this->tenancyManager->getCurrent());
    }

    public function testHasCurrent(): void
    {
        $this->tenancyManager->setCurrent($this->tenant);

        self::assertTrue($this->tenancyManager->hasCurrent());

        $this->tenancyManager->forgetCurrent();

        self::assertFalse($this->tenancyManager->hasCurrent());
    }

    public function tearDown(): void
    {
        TenancyManager::forgetInstance();

        parent::tearDown();
    }
}
