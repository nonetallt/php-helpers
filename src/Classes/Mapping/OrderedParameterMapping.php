<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Validation\Validators\ValueValidator;

class OrderedParameterMapping extends ParameterMapping
{
    public function __construct(string $name, int $position, $default = null, ValueValidator $rules = null, bool $isRequired = true)
    {
        $this->position = $position;
        parent::__construct($name, $default, $rules, $isRequired);
    }

    public function setPosition(int $position)
    {
        if($position < 0) {
            $msg = "Position can't be less than 0, $position given";
            throw new \InvalidArgumentException($msg);
        }

        $this->position;
    }

    public function getPosition() : int
    {
        return $this->position;
    }

    /**
     * @override
     */
    public function toArray() : array
    {
        $array = parent::toArray();
        $array['position'] = $this->position;
        return $array;
    }
}
