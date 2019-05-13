<?php

namespace Nonetallt\Helpers\Database\Mysql;

use Nonetallt\Helpers\Database\Common\Exceptions\SchemaException;

class SchemaObjectName
{
    /** 
     * Check if a string is a valid mysql schema name
     *
     * @throws Nonetallt\Helpers\Database\Common\Exceptions\SchemaException $e
     *
     * TODO better checks (unicode etc.) 
     */
    public static function validate(string $name)
    {
        if($name === '') {
            $msg = "Database schema name should not be empty";
            throw new SchemaException($msg);
        }

        if(preg_match('|^\d+$|', $name) === 1) {
            $msg = "Database schema name '$name' should not consist of only digits";
            throw new SchemaException($msg);
        }

        if(strlen($name) > 64) {
            $msg = "Database schema name '$name' is too long, maximum of 64 characters allowed";
            throw new SchemaException($msg);
        }

        $name = strtolower($name);
        $whitelisted = str_split('abcdefghijklmnopqrstuvwxyz0123456789_$');

        foreach(str_split($name) as $char) {
            if(in_array($char, $whitelisted)) continue;
            $msg = "Database schema name '$name' contains one or more illegal characters: $char";
            throw new SchemaException($msg);
        }
    }

    public static function isValid(string $name) : bool
    {
        try {
            self::validate($name);
            return true;
        }
        catch(SchemaException $e) {
            return false;
        }
    }
}
