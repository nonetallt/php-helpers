<?php

namespace Nonetallt\Helpers\Internet\Http\Common;

use Nonetallt\Helpers\Arrays\Traits\Arrayable;

class HttpHeader
{
    use Arrayable;

    private $name;
    private $value;

    public function __construct(string $name, string $value)
    {
        $this->setName($name);
        $this->setValue($value);
    }

    public function __toString()
    {
        return "$this->name:$this->value";
    }

    /**
     * TODO requires validation
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * TODO requires validation
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
