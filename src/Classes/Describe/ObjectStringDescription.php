<?php

namespace Nonetallt\Helpers\Describe;

class ObjectStringDescription extends StringDescription
{
    public static function description($value, StringDescriptionRepository $repo)
    {
        if(method_exists($value, '__toString')) return (string)$value;
        return get_class($value);
    }

    public static function prettyDescription($value, StringDescriptionRepository $repo)
    {
        return self::description($value, $repo);
    }
}
