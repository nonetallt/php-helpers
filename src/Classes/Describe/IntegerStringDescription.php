<?php

namespace Nonetallt\Helpers\Describe;

class IntegerStringDescription extends StringDescription
{
    public static function description($value, StringDescriptionRepository $repo)
    {
        return (string)$value;
    }

    public static function prettyDescription($value, StringDescriptionRepository $repo)
    {
        return "integer ($value)";
    }
}
