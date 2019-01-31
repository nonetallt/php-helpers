<?php

namespace Nonetallt\Helpers\Describe;

abstract class StringDescription
{
    public static abstract function description($value, StringDescriptionRepository $repo);

    public static abstract function prettyDescription($value, StringDescriptionRepository $repo);
}
