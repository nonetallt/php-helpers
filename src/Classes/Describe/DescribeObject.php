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

    public function describeValue()
    {
        $type = gettype($this->object);

        if(is_null($this->object)) $value = 'null';
        else if(is_bool($this->object)) $value = $this->object ? 'true' : 'false';
        else if($type === 'object') $value = get_class($this->object);
        else if(is_resource($this->object)) $value = 'resource';
        else if(is_scalar($this->object)) $value = (string)$this->object;
        else $value = gettype($this->object);

        return $value;
    }
}
