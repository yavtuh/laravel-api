<?php


if (!function_exists('cast_value')) {
    function cast_value($value, $type)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'float':
            case 'double':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'bool':
            case 'boolean':
                if (is_string($value)) {
                    return strtolower($value) === 'true';
                }
                return (bool)$value;
            default:
                return $value;
        }
    }
}

if (!function_exists('get_value_by_path')) {
    function get_value_by_path($array, $path)
    {
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return null;
            }
            $array = $array[$key];
        }
        return $array;
    }
}
