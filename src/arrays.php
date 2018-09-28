<?php

if(! function_exists('in_array_required')) {
    function in_array_required($value, array $array) 
    {
        if(! in_array($value, $array)) {
            $valid = implode(', ', $array);
            throw new \InvalidArgumentExceptioN("Value $value must be one of the values in array [$valid]");
        }
    }
}

if(! function_exists('required_in_array')) {
    function required_in_array($value, array $array) 
    {
        if(! in_array($value, $array)) {
            $valid = implode(', ', $array);
            throw new \InvalidArgumentExceptioN("Required value $value not found in array [$valid]");
        }
    }
}

if(! function_exists('array_keys_exist')) {
    function array_keys_exist(array $keys, array $arr) {
        return empty(array_diff($keys, array_keys($arr)));
    }
}

if(! function_exists('array_keys_missing')) {
    function array_keys_missing(array $keys, array $arr) {
        return array_diff($keys, array_keys($arr));
    }
}
