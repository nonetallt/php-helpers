<?php

namespace Nonetallt\Helpers\Database\Mysql;

use Nonetallt\Helpers\Database\Common\Exceptions\SchemaValidationException;

class SchemaObjectName
{
    /** 
     * Check if a string is a valid mysql schema name
     *
     * @throws Nonetallt\Helpers\Database\Common\Exceptions\SchemaValidationException $e
     *
     * TODO better checks (unicode etc.) 
     */
    public static function validate(string $name)
    {
        if($name === '') {
            $msg = "Database schema name should not be empty";
            throw new SchemaValidationException($msg);
        }

        if(preg_match('|^\d+$|', $name) === 1) {
            $msg = "Database schema name '$name' should not consist of only digits";
            throw new SchemaValidationException($msg);
        }

        if(strlen($name) > 64) {
            $msg = "Database schema name '$name' is too long, maximum of 64 characters allowed";
            throw new SchemaValidationException($msg);
        }

        $name = strtolower($name);
        $whitelisted = str_split('abcdefghijklmnopqrstuvwxyz0123456789_$');

        foreach(str_split($name) as $char) {
            if(in_array($char, $whitelisted)) continue;
            $msg = "Database schema name '$name' contains one or more illegal characters: $char";
            throw new SchemaValidationException($msg);
        }
    }

    public static function isValid(string $name) : bool
    {
        try {
            self::validate($name);
            return true;
        }
        catch(SchemaValidationException $e) {
            return false;
        }
    }
}
