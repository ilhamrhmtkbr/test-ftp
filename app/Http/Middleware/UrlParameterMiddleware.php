<?php

namespace ilhamrhmtkbr\App\Http\Middleware;

use ilhamrhmtkbr\App\Helper\UrlHelper;

class UrlParameterMiddleware implements Middleware
{
    public function before(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $parameters = [];
        if (strpos($requestUri, '?')) {
            $chest = explode('?', $requestUri);
            $items = array_unique($chest);
            foreach ($items as $key => $value) {
                if ($key == 0) {
                    continue;
                }

                if (str_contains($value, '&')) {
                    $fragments = explode('&', $value);
                    foreach ($fragments as $fragValue) {
                        if ($fragValue != '') {
                            $parameters[] = $fragValue;
                        }
                    }
                } else {
                    $parameters[] = $value;
                }
            }

            $newParams = [];

            if (count($parameters) > 1) {
                foreach ($parameters as $paramKey => $paramValue) {
                    if ($paramKey == 0) {
                        $newParams[] = $paramValue;
                        continue;
                    }

                    foreach ($newParams as $newParamKey => $newParamValue) {
                        $elementParameters = explode('=', $paramValue);
                        $elementNewParameters = explode('=', $newParamValue);
                        if ($elementParameters[0] == $elementNewParameters[0]) {
                            unset($newParams[$newParamKey]);
                        }
                    }

                    $newParams[] = $paramValue;
                }
                $queryString = UrlHelper::getPathInfo() . '?' . implode('&', $newParams);
            } else {
                $queryString = UrlHelper::getPathInfo() . '?' . implode('', $parameters);
            }

            if ($requestUri !== $queryString) {
                header('Location: ' . $queryString);
            }
        }
    }
}
