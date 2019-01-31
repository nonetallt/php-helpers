<?php

namespace Nonetallt\Helpers\Describe;

class BooleanStringDescription extends StringDescription
{
    public static function description($value, StringDescriptionRepository $repo)
    {
        return $value ? 'true' : 'false';
    }

    public static function prettyDescription($value, StringDescriptionRepository $repo)
    {
        return $value ? 'boolean (true)' : 'boolean (false)';
    }
}
