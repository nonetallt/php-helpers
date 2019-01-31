<?php

namespace Nonetallt\Helpers\Describe;

class ArrayStringDescription extends StringDescription
{
    private static function serialize($value, StringDescriptionRepository $repo)
    {
        $array = [];
        foreach($value as $key => $value) {
            $array[$key] = $repo->getDescription($value);
        }
        return $array;
    }

    public static function description($value, StringDescriptionRepository $repo)
    {
        return json_encode(self::serialize($value, $repo));
    }

    public static function prettyDescription($value, StringDescriptionRepository $repo)
    {
        return json_encode(self::serialize($value, $repo), JSON_PRETTY_PRINT);
    }
}
