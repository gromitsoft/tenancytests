<?php

namespace GromIT\TenancyTests\Tests\Tasks\SwitchTenant;

use GromIT\Tenancy\Tasks\SwitchTenant\ChangeCachePrefix;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;
use Illuminate\Support\Facades\Event;

class PrefixCacheTest extends TenancyPluginTestCase
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

    public function testChangesCachePrefix(): void
    {
        $original = config('cache.prefix');

        $task = new ChangeCachePrefix();
        $task->makeCurrent($this->tenant);

        self::assertEquals("{$original}_tenant_{$this->tenant->id}", config('cache.prefix'));

        $task->forgetCurrent();

        self::assertEquals($original, config('cache.prefix'));
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
