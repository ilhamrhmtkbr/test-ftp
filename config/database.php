<?php

function getDatabaseConfig(): array
{
    $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
    $dbName = $_ENV['DB_NAME'] ?? 'talent_hub';
    $dbUser = $_ENV['DB_USERNAME'] ?? 'root';
    $dbPass = $_ENV['DB_PASSWORD'] ?? 'root';

    return [
        'database-pdo' => [
            'test' => [
                'url' => 'mysql:host=localhost:3306;dbname=talent_hub_test',
                'username' => 'root',
                'password' => ''
            ],
            'prod' => [
                'url' => "mysql:host=". $dbHost .";dbname=" . $dbName,
                'username' => $dbUser,
                'password' => $dbPass
            ],
            'docker' => [
                'url' => 'mysql:host=talent-hub-mysql;dbname=talent_hub',
                'username' => 'root',
                'password' => 'root'
            ]
        ],

        'database-mysqli' => [
            'test' => [
                'host' => 'localhost',
                'user' => 'root',
                'password' => '',
                'db_name' => 'talent_hub_test'
            ],
            'prod' => [
                'host' => $dbHost,
                'user' => $dbUser,
                'password' => $dbPass,
                'db_name' => $dbName
            ],
            'docker' => [
                'host' => 'talent-hub-mysql',
                'user' => 'root',
                'password' => 'root',
                'db_name' => 'talent_hub'
            ]
        ]
    ];
}
