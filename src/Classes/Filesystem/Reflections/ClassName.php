<?php

namespace Nonetallt\Helpers\Filesystem\Reflections;

use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;

class ClassName
{
    use LazyLoadsProperties;

    private $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function __toString() : string
    {
        return $this->class;
    }

    public function lazyLoadNamespaceParts()
    {
        return explode('\\', $this->class);
    }

    public function lazyLoadNamespace() : string
    {
        $parts = $this->getNamespaceParts();
        $partsCount = count($parts);

        if($partsCount === 1) {
            return '';
        }

        return implode('\\', array_slice($parts, 0, $partsCount -1));
    }

    public function lazyLoadShortName() : string
    {
        $parts = $this->getNamespaceParts();
        $partsCount = count($parts);

        if($partsCount === 1) {
            return $parts[0];
        }

        return $parts[$partsCount -1];
    }
}
