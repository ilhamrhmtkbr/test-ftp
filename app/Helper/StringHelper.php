<?php

namespace ilhamrhmtkbr\App\Helper;

class StringHelper
{
    public static function toCapitalize($string)
    {
        if ($string) {
            if (strpos($string, '_')) {
                $spacedString = str_replace('_', ' ', $string);
                return ucwords($spacedString);
            } else {
                $spacedString = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);
                return ucfirst($spacedString);
            }
        }
    }
}
