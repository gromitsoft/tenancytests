<?php

namespace GromIT\TenancyTests\Tests\Actions\TenantAwareness;

use GromIT\Tenancy\Actions\TenantAwareness\MakeModelUseTenantConnection;
use GromIT\Tenancy\Concerns\UsesTenancyConfig;
use GromIT\TenancyTests\Models\Entry;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class MakeModelUseTenantConnectionTest extends TenancyPluginTestCase
{
    use UsesTenancyConfig;

    /**
     * @throws \October\Rain\Exception\SystemException
     */
    public function testMakeModelUseTenantConnection(): void
    {
        MakeModelUseTenantConnection::make()->execute(Entry::class);

        $entry = new Entry();

        self::assertSame($entry->getConnectionName(), $this->getTenantConnectionName());
    }
}
