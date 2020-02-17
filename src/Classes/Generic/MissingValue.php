<?php

namespace Nonetallt\Helpers\Generic;

/**
 * Simple declaration that a value is missing
 */
class MissingValue
{
    public function __toString() : string
    {
        return '[missing value]';
    }
}
