<?php

function getTestConfig(string $item): ?string
{
    $config = [
        'domain' => 'localhost',
        'base_uri' => 'http://localhost' //karena di docker
    ];

    return $config[$item];
}
