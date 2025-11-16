<?php

function getDatabaseConfig(): array
{
    return [
        'database-pdo' => [
            'test' => [
                'url' => 'mysql:host=localhost:3306;dbname=talent_hub_test',
                'username' => 'root',
                'password' => ''
            ],
            'prod' => [
                'url' => "mysql:host=". $_ENV['DB_HOST'] .";dbname=" . $_ENV['DB_NAME'],
                'username' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD']
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
                'host' => $_ENV['DB_HOST'],
                'user' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
                'db_name' => $_ENV['DB_NAME']
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
