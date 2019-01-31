<?php

namespace Nonetallt\Helpers\Describe;

class ResourceStringDescription extends StringDescription
{
    public static function description($value, StringDescriptionRepository $repo)
    {
        return get_resource_type($value);
    }

    public static function prettyDescription($value, StringDescriptionRepository $repo)
    {
        $type = get_resource_type($value);
        return "resource ($type)";
    }
}
