<?php

namespace ilhamrhmtkbr\App\Config;

use mysqli;

class Database
{
    private static ?\PDO $pdo = null;
    private static ?\mysqli $mysqli = null;

    public static function getConnection(string $env = 'docker'): \PDO
    {
        if (self::$pdo == null) {
            require_once __DIR__ . '/../../config/database.php';
            $config = getDatabaseConfig();
            self::$pdo = new \PDO(
                $config['database-pdo'][$env]['url'],
                $config['database-pdo'][$env]['username'],
                $config['database-pdo'][$env]['password']
            );
        }

        return self::$pdo;
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction()
    {
        self::$pdo->rollBack();
    }

    public static function getConnMysqli(string $env = 'docker'): \mysqli
    {
        if (self::$mysqli == null) {
            require_once __DIR__ . '/../../config/database.php';
            $config = getDatabaseConfig();
            self::$mysqli = new mysqli(
                $config['database-mysqli'][$env]['host'],
                $config['database-mysqli'][$env]['user'],
                $config['database-mysqli'][$env]['password'],
                $config['database-mysqli'][$env]['db_name']
            );
        }

        return self::$mysqli;
    }
}
