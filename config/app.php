<?php

function getAppConfig(string $item): string
{
    $config = [
        'name' => 'Talent Hub'
    ];

    return $config[$item];
}
