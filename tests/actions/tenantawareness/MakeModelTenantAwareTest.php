<?php

namespace GromIT\TenancyTests\Tests\Actions\TenantAwareness;

use Illuminate\Support\Facades\Event;
use October\Rain\Database\Behaviors\Sortable;
use October\Rain\Exception\SystemException;
use GromIT\Tenancy\Actions\CurrentTenant\ForgetCurrentTenant;
use GromIT\Tenancy\Actions\CurrentTenant\MakeTenantCurrent;
use GromIT\Tenancy\Actions\TenantAwareness\MakeModelTenantAware;
use GromIT\Tenancy\Classes\TenantAwareScope;
use GromIT\TenancyTests\Models\Entry;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class MakeModelTenantAwareTest extends TenancyPluginTestCase
{
    use CreatesTenants;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $tenant1;

    /**
     * @var \GromIT\Tenancy\Models\Tenant
     */
    private $tenant2;

    public function setUp(): void
    {
        parent::setUp();

        Event::fakeFor(function () {
            $this->tenant1 = $this->createTenant();
            $this->tenant2 = $this->createTenant();
        });
    }

    /**
     * @throws \October\Rain\Exception\SystemException
     * @throws \GromIT\Tenancy\Exceptions\CurrentTenantIsNotSet
     */
    public function testWorksWithModelsOnly(): void
    {
        $this->expectException(SystemException::class);

        MakeModelTenantAware::make()->execute(self::class);
    }

    /**
     * @throws \October\Rain\Exception\SystemException
     * @throws \GromIT\Tenancy\Exceptions\CurrentTenantIsNotSet
     */
    public function testMakesModelTenantAware(): void
    {
        MakeModelTenantAware::make()->execute(Entry::class);

        MakeTenantCurrent::make()->execute($this->tenant1);

        $entry1       = new Entry();
        $entry1->name = 'test entry 1';
        $entry1->save();

        self::assertArrayHasKey('tenantable', $entry1->morphOne);
        $this->assertDatabaseHas(
            'gromit_tenancy_tenantables',
            [
                'tenantable_type' => Entry::class,
                'tenantable_id'   => $entry1->id,
                'tenant_id'       => $this->tenant1->id,
            ]
        );

        $entry2       = new Entry();
        $entry2->name = 'test entry 2';
        $entry2->save();

        self::assertArrayHasKey('tenantable', $entry2->morphOne);
        $this->assertDatabaseHas(
            'gromit_tenancy_tenantables',
            [
                'tenantable_type' => Entry::class,
                'tenantable_id'   => $entry2->id,
                'tenant_id'       => $this->tenant1->id,
            ]
        );

        MakeTenantCurrent::make()->execute($this->tenant2);

        $entry3       = new Entry();
        $entry3->name = 'test entry 3';
        $entry3->save();

        self::assertArrayHasKey('tenantable', $entry3->morphOne);
        $this->assertDatabaseHas(
            'gromit_tenancy_tenantables',
            [
                'tenantable_type' => Entry::class,
                'tenantable_id'   => $entry3->id,
                'tenant_id'       => $this->tenant2->id,
            ]
        );

        $entry4       = new Entry();
        $entry4->name = 'test entry 4';
        $entry4->save();

        self::assertArrayHasKey('tenantable', $entry4->morphOne);
        $this->assertDatabaseHas(
            'gromit_tenancy_tenantables',
            [
                'tenantable_type' => Entry::class,
                'tenantable_id'   => $entry4->id,
                'tenant_id'       => $this->tenant2->id,
            ]
        );

        MakeTenantCurrent::make()->execute($this->tenant1);

        $tenant1Entries = Entry::query()->get();

        self::assertEquals(2, $tenant1Entries->count());

        foreach ($tenant1Entries as $tenant1Entry) {
            self::assertSame($this->tenant1->id, $tenant1Entry->tenantable->tenant_id);
        }

        MakeTenantCurrent::make()->execute($this->tenant2);

        $tenant2Entries = Entry::query()->get();

        self::assertEquals(2, $tenant2Entries->count());

        foreach ($tenant2Entries as $tenant2Entry) {
            self::assertSame($this->tenant2->id, $tenant2Entry->tenantable->tenant_id);
        }
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        ForgetCurrentTenant::make()->execute();

        Event::fakeFor(function () {
            $this->tenant1->delete();
            $this->tenant2->delete();
        });

        parent::tearDown();
    }
}
