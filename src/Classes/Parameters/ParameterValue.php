<?php

namespace Nonetallt\Helpers\Parameters;

class ParameterValue
{
    private $name;
    private $value;

    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function toArray()
    {
        return [$this->name => $this->value];
    }
}
