<?php

namespace Nonetallt\Helpers\Strings\Languages;

use Nonetallt\Helpers\Strings\Str;

abstract class Language
{
    /**
     * Get name of the language
     *
     */
    public static function getName() : string
    {
        return Str::removePrefix(get_class($this), __NAMESPACE__);
    }

    /**
     * Get an array containing every character in the alphabet
     *
     */
    abstract static function alphabet(bool $includeUppercase = false) : array;
}
