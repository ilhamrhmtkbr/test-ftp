<?php

function getEmailConfig(): array
{
    return [
        'email' => [
            'username' => $_ENV['MAIL_USERNAME'],
            'password' => $_ENV['MAIL_PASSWORD']
        ]
    ];
}
