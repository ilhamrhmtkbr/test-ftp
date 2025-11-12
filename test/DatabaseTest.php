<?php

namespace ilhamrhmtkbr\Test;

use ilhamrhmtkbr\App\Config\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function test_get_connection()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection);
    }

    public function test_get_connection_singleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertSame($connection1, $connection2);
    }
}
