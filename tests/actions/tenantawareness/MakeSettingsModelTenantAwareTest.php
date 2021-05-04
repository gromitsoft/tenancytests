<?php

namespace GromIT\TenancyTests\Tests\Actions\TenantAwareness;

use Illuminate\Support\Facades\Event;
use October\Rain\Exception\SystemException;
use GromIT\Tenancy\Actions\CurrentTenant\ForgetCurrentTenant;
use GromIT\Tenancy\Actions\CurrentTenant\MakeTenantCurrent;
use GromIT\Tenancy\Actions\TenantAwareness\MakeSettingsModelTenantAware;
use GromIT\Tenancy\Tasks\DeleteTenant\DeleteDatabase;
use GromIT\TenancyTests\Models\Post;
use GromIT\TenancyTests\Models\Settings;
use GromIT\TenancyTests\Tests\CreatesTenants;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class MakeSettingsModelTenantAwareTest extends TenancyPluginTestCase
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

        $this->tenant->database_name = $this->getDefaultDatabaseName() . '_tenant_' . $this->tenant->id;
        $this->tenant->save();

        $this->createDatabase($this->tenant);

        config(["database.connections.{$this->getTenantConnectionName()}.database" => $this->tenant->database_name]);

        $this->createMigrationsTable();
        $this->migrateBaseTables();

        config(["database.connections.{$this->getTenantConnectionName()}.database" => null]);

        MakeTenantCurrent::make()->execute($this->tenant);
    }

    public function testWorksWithModelsOnly(): void
    {
        $this->expectException(SystemException::class);

        MakeSettingsModelTenantAware::make()->execute(self::class);
    }

    /**
     * @throws \October\Rain\Exception\SystemException
     */
    public function testWorksWithSettingsModelsOnly(): void
    {
        $this->expectException(SystemException::class);

        MakeSettingsModelTenantAware::make()->execute(Post::class);
    }

    /**
     * @throws \October\Rain\Exception\SystemException
     */
    public function testMakesSettingsModelTenantAware(): void
    {
        MakeSettingsModelTenantAware::make()->execute(Settings::class);

        /** @var Settings|\System\Behaviors\SettingsModel $settings */
        $settings = new Settings();

        self::assertStringEndsWith("_tenant_{$this->tenant->id}", $settings->settingsCode);

        $settings->set('name', 'testname');
        $settings->set('surname', 'testsurname');

        $this->assertDatabaseHas(
            'system_settings',
            ['item' => $settings->settingsCode]
        );

        unset($settings);
    }

    /**
     * @throws \Exception
     */
    public function tearDown(): void
    {
        ForgetCurrentTenant::make()->execute();

        Settings::clearExtendedClasses();

        $deleteDatabase = new DeleteDatabase();
        $deleteDatabase->handle($this->tenant);

        Event::fakeFor(function () {
            $this->tenant->delete();
        });

        parent::tearDown();
    }
}
