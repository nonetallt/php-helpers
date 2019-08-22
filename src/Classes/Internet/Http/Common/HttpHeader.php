<?php

namespace Nonetallt\Helpers\Internet\Http\Common;

class HttpHeader
{
    private $name;
    private $value;

    public function __construct(string $name, string $value)
    {
        $this->setName($name);
        $this->setValue($value);
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
