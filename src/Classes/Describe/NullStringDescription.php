<?php

namespace Nonetallt\Helpers\Describe;

class NullStringDescription extends StringDescription
{
    public static function description($value, StringDescriptionRepository $repo)
    {
        return '';
    }

    public static function prettyDescription($value, StringDescriptionRepository $repo)
    {
        return 'NULL';
    }
}
