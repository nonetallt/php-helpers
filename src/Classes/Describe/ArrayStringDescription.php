<?php

namespace Nonetallt\Helpers\Describe;

class ArrayStringDescription extends StringDescription
{
    private static function serialize($value, bool $pretty, StringDescriptionRepository $repo)
    {
        $array = [];
        foreach($value as $key => $value) {
            $array[$key] = $repo->getDescription($value, $pretty);
        }
        return $array;
    }

    public static function description($value, StringDescriptionRepository $repo)
    {
        return json_encode(self::serialize($value, false, $repo));
    }

    public static function prettyDescription($value, StringDescriptionRepository $repo)
    {
        return json_encode(self::serialize($value, true, $repo), JSON_PRETTY_PRINT);
    }
}
