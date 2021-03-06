<?php

namespace Nonetallt\Helpers\Templating;

use Nonetallt\Helpers\Templating\Exceptions\AccessorException;

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

    public function isset(string $path, $values)
    {
        if($values === null) return false;

        if(! is_array($values)) {
            $msg = "Not implemented for non-array values";
            throw new \InvalidArgumentException($msg);
        }

        /* If path does not have accessor */
        if(! $this->isContainedInString($path)) {
            if(! isset($values[$path])) return false;
            return true;
        }

        $pathParts = explode($this->format, $path);

        /* Remove first part from path */
        $current = array_splice($pathParts, 0, 1)[0];

        /* Check for non-existent path (undefined index) */
        if(! isset($values[$current])) return false;

        return $this->isset(implode($this->format, $pathParts), $values[$current]);
    }

    /**
     * @throws Nonetallt\Helpers\Templating\Exceptions\AccessorException
     */
    public function getNestedValue(string $path, $values)
    {
        if(! $this->isset($path, $values)) {
            $msg = "Path $path does not exist in supplied values";
            throw new AccessorException($msg);
        } 

        $currentValue = $values;
        foreach(explode($this->format, $path) as $pathPart) {
            $currentValue = $currentValue[$pathPart];
        }

        return $currentValue;
    }
}
