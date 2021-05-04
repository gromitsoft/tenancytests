<?php

namespace GromIT\TenancyTests\Tests\Classes;

use GromIT\Tenancy\Classes\Helpers;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class HelpersTest extends TenancyPluginTestCase
{
    public function testIsPluginTenantAware(): void
    {
        self::assertFalse(Helpers::isPluginTenantAware('GromIT.Tenancy'));
        self::assertTrue(Helpers::isPluginTenantAware('GromIT.TenancyTests'));
    }
}
