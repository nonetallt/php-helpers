<?php

namespace Nonetallt\Helpers\Describe;

class DescribeObject
{
    private $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    public function describeType()
    {
        $type = gettype($this->object);

        /* Get class instead if type is an object */
        if($type === 'object') $type = get_class($this->object);

        return $type;
    }
}
