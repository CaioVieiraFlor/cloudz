<?php

namespace CloudZ\Utility;

class ArrayHelper
{
    public static function get(array $array, string $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}
