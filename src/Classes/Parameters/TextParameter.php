<?php

namespace Nonetallt\Helpers\Parameters;

class TextParameter extends Parameter
{
    public static function getAvailableOptions()
    {
        return [];
    }

    public static function getDefaultOptions()
    {
        return [];
    }

    public function validateValue($value)
    {
        return is_string($value);
    }
}
