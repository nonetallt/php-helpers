<?php

namespace Nonetallt\Helpers\Describe;

class DescribeObject
{
    private $object;
    private $stringDescriptionRepository;

    public function __construct($object, StringDescriptionRepository $stringDescriptionRepository = null)
    {
        $this->object = $object;
        $this->stringDescriptionRepository = $stringDescriptionRepository;
    }

    public function __toString()
    {
        return $this->describeAsString();
    }

    public function describeStringDiff(string $other) : array
    {
        if(! is_string($this->object)) throw new \Exception("Object must be a string");
        $diff = [];

        for($n = 0; $n < strlen($this->object); $n++) {
            $s1 = substr($this->object, $n, 1);
            $s2 = substr($other, $n, 1);

            if($s1 !== $s2) $diff[$n] = "'$s1' !== '$s2'";
        }

        return $diff;
    }

    public function describeAsString(bool $pretty = true)
    {
        $repo = $this->getStringDescriptionRepository();
        $repo->setPretty($pretty);

        return $repo->getDescription($this->object);

        throw new \Exception("Cannot describe {$this->describeType()} as string, value is not string convertable");
    }

    public function getStringDescriptionRepository()
    {
        if(is_null($this->stringDescriptionRepository)) {
            $this->stringDescriptionRepository = new StringDescriptionRepository();
        }

        return $this->stringDescriptionRepository;
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
