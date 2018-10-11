<?php

namespace Nonetallt\Helpers\Parameters;

use Nonetallt\Helpers\Arrays\TypedArray;

class ParameterValues
{
    private $data;

    public function __construct(array $values)
    {
        $this->data = TypedArray::create(ParameterValue::class, $values);
    }

    public static function fromArray(array $array)
    {
        $values = [];
        foreach($array as $key => $value) {
            $values[] = new ParameterValue($key, $value);
        }
        return new self($values);
    }

    public function toArray()
    {
        $values = [];
        foreach($this->data as $value) {
            $values = array_merge($values, $value->toArray());
        }
        return $values;
    }
}
