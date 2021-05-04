<?php

namespace GromIT\TenancyTests\Tests;

use PluginTestCase;
use System\Classes\PluginManager;

class TenancyPluginTestCase extends PluginTestCase
{
    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Register the plugins to make features like file configuration available
        $pluginManager->registerAll(true);

        // Boot all the plugins to test with dependencies of this plugin
        $pluginManager->bootAll(true);

        // APP_ENV=testing not working for some reason
        config([
            'database'                                      => config('testing.database'),
            'gromit.tenancy::database.tenant_aware_plugins' => ['GromIT.TenancyTests'],
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Get the plugin manager
        $pluginManager = PluginManager::instance();

        // Ensure that plugins are registered again for the next test
        $pluginManager->unregisterAll();
    }
}
