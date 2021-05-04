<?php

namespace GromIT\TenancyTests\Tests\DatabaseCreators;

use GromIT\Tenancy\DatabaseCreators\MysqlDatabaseCreator;
use GromIT\TenancyTests\Tests\TenancyPluginTestCase;

class MysqlDatabaseManagerTest extends TenancyPluginTestCase
{
    public function testCreateDatabase(): void
    {
        $manager = new MysqlDatabaseCreator();

        $dbName = 'testing__' . str_random();

        $manager->createDatabase($dbName);

        self::assertTrue($manager->databaseExists($dbName));

        $manager->dropDatabase($dbName);
    }

    public function testDropDatabase(): void
    {
        $manager = new MysqlDatabaseCreator();

        $dbName = 'testing__' . str_random();

        $manager->createDatabase($dbName);

        self::assertTrue($manager->databaseExists($dbName));

        $manager->dropDatabase($dbName);

        self::assertFalse($manager->databaseExists($dbName));
    }

    public function testDatabaseExists(): void
    {
        $manager = new MysqlDatabaseCreator();

        $dbName = 'testing__' . str_random();

        $manager->createDatabase($dbName);

        self::assertTrue($manager->databaseExists($dbName));

        $manager->dropDatabase($dbName);
    }
}
