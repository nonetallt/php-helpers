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

    public function getNestedValue(string $path, $values)
    {
        if(! is_array($values)) return $values;
        if($path === '') throw new \InvalidArgumentException("Path cannot be an empty string");

        $pathParts = explode($this->format, $path);

        /* Remove first part from path */
        $current = array_splice($pathParts, 0, 1)[0];

        /* Check for non-existent path (undefined index) */
        if(! isset($values[$current])) throw new \InvalidArgumentException("Path '$current' is not set");

        $value = $values[$current];

        return $this->getNestedValue(implode($this->format, $pathParts), $value);
    }
}
