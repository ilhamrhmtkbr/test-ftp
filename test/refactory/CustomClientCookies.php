<?php

namespace ilhamrhmtkbr\Test\refactory;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use ilhamrhmtkbr\App\Facades\Session;
use ilhamrhmtkbr\App\Helper\FormSessionHelper;

require_once __DIR__ . '/../../config/test.php';

class CustomClientCookies
{
    public static function createClientWithCookieAuthMiddleware(string $email): Client
    {
        $cookies = new CookieJar();
        $cookies->setCookie(new SetCookie([
            'Name'     => Session::$COOKIE_NAME,
            'Value'    => base64_encode($email),
            'Domain'   => getTestConfig('domain'),
            'Path'     => '/',
            'Expires'  => time() + 3600
        ]));

        return new Client(['base_uri' => getTestConfig('base_uri'), 'cookies' => $cookies]);
    }

    public static function createClientWithCookieAuthMiddlewareAndFormSession(string $email, string $session): Client
    {
        $cookies = new CookieJar();
        $cookies->setCookie(new SetCookie([
            'Name'     => Session::$COOKIE_NAME,
            'Value'    => base64_encode($email),
            'Domain'   => getTestConfig('domain'),
            'Path'     => '/',
            'Expires'  => time() + 3600
        ]));
        $cookies->setCookie(new SetCookie([
            'Name'     => FormSessionHelper::$COOKIE_NAME,
            'Value'    => $session,
            'Domain'   => getTestConfig('domain'),
            'Path'     => '/',
            'Expires'  => time() + 3600
        ]));
        return new Client(['base_uri' => getTestConfig('base_uri'), 'cookies' => $cookies]);
    }

    public static function createClientWithCookieFormSession(string $session): Client
    {
        $cookies = new CookieJar();
        $cookies->setCookie(new SetCookie([
            'Name'     => FormSessionHelper::$COOKIE_NAME,
            'Value'    => $session,
            'Domain'   => getTestConfig('domain'),
            'Path'     => '/',
            'Expires'  => time() + 3600
        ]));

        return new Client(['base_uri' => getTestConfig('base_uri'), 'cookies' => $cookies]);
    }
}
