<?php

namespace Nonetallt\Helpers\Parameters;

class RecursiveAccessor
{
    private $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function isContainedInString(string $value)
    {
        return strpos($value, $this->format) !== false;
    }

    public function getFormat()
    {
        return $this->format;
    }
}
