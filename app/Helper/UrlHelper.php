<?php

namespace ilhamrhmtkbr\App\Helper;

class UrlHelper
{
    public static function getParamData(): array // simulasi misal $url = 'dsa/asd?a=1&b=2
    {
        $resultParams = [
            'page' => 1,
            'keyword' => '',
            'orderBy' => 'DESC',
            'status' => ''
        ];

        if (strpos($_SERVER['REQUEST_URI'], '?')) {
            $URL = explode('?', $_SERVER['REQUEST_URI']);
            $queries = explode('&', $URL[1]); // $queries = ['a=1', 'b=2]
            $removeDupplicated = array_unique($queries); // jaga jaga kalo $queries = ['a=1', 'a=1']
            foreach ($removeDupplicated as $key => $value) {
                if (strpos($value, '=')) {
                    $str = explode('=', $value);
                    if ($str[0] != '') {
                        if (!empty($resultParams)) {
                            foreach ($resultParams as $paramKey => $paramValue) {
                                if ($paramKey == $str[0]) {
                                    unset($resultParams[$paramKey]);
                                }
                            }
                        }
                        $resultParams[$str[0]] = urldecode($str[1]); // buat ganti kalo ada karakter + dengan spasi
                    }
                }
            }
        }

        return $resultParams;
    }

    public static function getPathInfo(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $pathInfo = '/';

        if ($requestUri != $pathInfo) {
            if (strpos($requestUri, '?') !== false) {
                $elements = explode('?', $requestUri);
                if (self::isValidUrl($elements[0])) {
                    $pathInfo = strtolower($elements[0]);
                } else {
                    // DEBUG: Log invalid URL
                    DebugHelper::log("Invalid URL format: " . $elements[0]);
                }
            } else {
                if (self::isValidUrl($requestUri)) {
                    $pathInfo = strtolower($requestUri);
                } else {
                    // DEBUG: Log invalid URL
                    DebugHelper::log("Invalid URL format: " . $requestUri);
                }
            }
        }

        // Remove trailing slash (kecuali root)
        if ($pathInfo !== '/' && substr($pathInfo, -1) === '/') {
            $pathInfo = substr($pathInfo, 0, -1);
        }

        return $pathInfo;
    }

    private static function isValidUrl(string $url): bool
    {
        // Allow: huruf, angka, /, -, _, dan titik
        // Tapi tidak allow karakter berbahaya seperti <, >, ", ', dll
        $pattern = '/^[a-zA-Z0-9\/\-_.]+$/';

        if (preg_match($pattern, $url)) {
            return true;
        }

        // Log untuk debugging
        DebugHelper::log("Invalid URL rejected: " . $url);
        return false;
    }
}
