<?php

namespace GromIT\TenancyTests\Tests\TenantFinders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\TenantFinders\DomainTenantFinder;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class DomainTenantFinderTest extends TenancyPluginTestCase
{
    use CreatesTenants;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $activeTenant;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $inactiveTenant;

    /**
     * @var \GromIT\Tenancy\TenantFinders\DomainTenantFinder
     */
    private $tenantFinder;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenantFinder = new DomainTenantFinder();

        Event::fakeFor(function () {
            $this->activeTenant = $this->createTenant();

            $this->inactiveTenant            = $this->createTenant();
            $this->inactiveTenant->is_active = false;
            $this->inactiveTenant->save();
        });
    }

    public function testFindTenantByDomain(): void
    {
        $this->activeTenant->domains()->create([
            'url'       => 'activedomain.tld',
            'is_active' => true
        ]);

        $request = Request::create('https://activedomain.tld');
        $found   = $this->tenantFinder->findForRequest($request);

        self::assertSame($this->activeTenant->id, $found->id ?? null);

        $this->activeTenant->domains()->create([
            'url'       => 'inactivedomain.tld',
            'is_active' => false
        ]);

        $request = Request::create('https://inactivedomain.tld');
        $found   = $this->tenantFinder->findForRequest($request);

        self::assertNull($found);

        $this->inactiveTenant->domains()->create([
            'url'       => 'activedomain1.tld',
            'is_active' => true
        ]);

        $request = Request::create('https://activedomain1.tld');
        $found   = $this->tenantFinder->findForRequest($request);

        self::assertNull($found);

        $this->inactiveTenant->domains()->create([
            'url'       => 'inactivedomain1.tld',
            'is_active' => false
        ]);

        $request = Request::create('https://inactivedomain1.tld');
        $found   = $this->tenantFinder->findForRequest($request);

        self::assertNull($found);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        Event::fakeFor(function () {
            $this->activeTenant->delete();

            $this->inactiveTenant->delete();
        });

        parent::tearDown();
    }
}
