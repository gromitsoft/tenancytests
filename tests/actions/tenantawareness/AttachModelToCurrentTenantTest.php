<?php

namespace GromIT\TenancyTests\Tests\Actions\TenantAwareness;

use Illuminate\Support\Facades\Event;
use GromIT\Tenancy\Actions\TenantAwareness\AttachModelToCurrentTenant;
use GromIT\Tenancy\Classes\TenancyManager;
use GromIT\Tenancy\Exceptions\CurrentTenantIsNotSet;
use GromIT\TenancyTests\Models\Entry;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class AttachModelToCurrentTenantTest extends TenancyPluginTestCase
{
    use CreatesTenants;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $tenant;

    /**
     * @var \GromIT\TenancyTests\Models\Entry
     */
    private $entry;

    public function setUp(): void
    {
        parent::setUp();

        Event::fakeFor(function () {
            $this->tenant = $this->createTenant();
        });

        $this->entry = Entry::create([
            'name' => 'test entry'
        ]);
    }

    /**
     * @throws \GromIT\Tenancy\Exceptions\CurrentTenantIsNotSet
     */
    public function testNeedsCurrentTenant(): void
    {
        $this->expectException(CurrentTenantIsNotSet::class);

        AttachModelToCurrentTenant::make()->execute($this->entry);
    }

    /**
     * @throws \GromIT\Tenancy\Exceptions\CurrentTenantIsNotSet
     */
    public function testModelAttachesToCurrentTenant(): void
    {
        TenancyManager::instance()->setCurrent($this->tenant);

        AttachModelToCurrentTenant::make()->execute($this->entry);

        $this->assertDatabaseHas(
            'gromit_tenancy_tenantables',
            [
                'tenantable_type' => Entry::class,
                'tenantable_id'   => $this->entry->id,
                'tenant_id'       => $this->tenant->id,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        TenancyManager::instance()->forgetCurrent();

        Event::fakeFor(function () {
            $this->tenant->delete();
        });

        $this->entry->delete();

        parent::tearDown();
    }
}
