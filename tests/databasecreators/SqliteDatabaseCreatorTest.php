<?php

namespace GromIT\TenancyTests\Tests\DatabaseCreators;

use GromIT\Tenancy\DatabaseCreators\SqliteDatabaseCreator;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class SqliteDatabaseCreatorTest extends TenancyPluginTestCase
{
    public function testCreateDatabase(): void
    {
        if (!$this->isEnabled()) {
            self::markTestSkipped('current database driver != sqlite');
        }

        $manager = new SqliteDatabaseCreator();

        $dbName = storage_path('temp/' . 'testing__' . str_random());

        $manager->createDatabase($dbName);

        self::assertTrue($manager->databaseExists($dbName));

        $manager->dropDatabase($dbName);
    }

    public function testDropDatabase(): void
    {
        if (!$this->isEnabled()) {
            self::markTestSkipped('current database driver != sqlite');
        }

        $manager = new SqliteDatabaseCreator();

        $dbName = storage_path('temp/' . 'testing__' . str_random());

        $manager->createDatabase($dbName);

        self::assertTrue($manager->databaseExists($dbName));

        $manager->dropDatabase($dbName);

        self::assertFalse($manager->databaseExists($dbName));
    }

    public function testDatabaseExists(): void
    {
        if (!$this->isEnabled()) {
            self::markTestSkipped('current database driver != sqlite');
        }

        $manager = new SqliteDatabaseCreator();

        $dbName = storage_path('temp/' . 'testing__' . str_random());

        $manager->createDatabase($dbName);

        self::assertTrue($manager->databaseExists($dbName));

        $manager->dropDatabase($dbName);
    }

    private function isEnabled(): bool
    {
        $defaultConnection = config('database.default');

        $driver = config("database.connections.$defaultConnection.driver");

        return $driver === 'sqlite';
    }
}
