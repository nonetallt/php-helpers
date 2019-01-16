<?php

namespace Nonetallt\Helpers\Database\Mysql;

class SchemaObjectName
{

    public static function isValid(string $definition)
    {
        $definition = strtolower($definition);
        $alphaNumeric = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $special = '_$';

        $validChars = array_merge(str_split($alphaNumeric), str_split($special));

        foreach(str_split($definition) as $char) {
            if(! in_array($char, $validChars)) return false;
        }

        return true;
    }
}
