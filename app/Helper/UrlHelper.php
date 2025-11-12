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
                }
            } else {
                if (self::isValidUrl($requestUri)) {
                    $pathInfo = strtolower($requestUri);
                }
            }
        }

        return $pathInfo;
    }

    private static function isValidUrl(string $url): bool
    {
        $pattern = '/^[a-zA-Z\/\-]+$/'; // hanya boleh mengandung huruf dan '/'
        if (preg_match($pattern, $url)) {
            return true;
        } else {
            return false;
        }
    }
}
