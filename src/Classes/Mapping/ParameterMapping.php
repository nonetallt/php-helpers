<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Validation\ValueValidator;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;

class ParameterMapping
{
    private $name;
    private $default;
    private $validator;
    private $isRequired;

    public function __construct(string $name, $default = MissingValue, ValueValidator $validator = null, bool $isRequired = true)
    {
        $this->setName($name);
        $this->setDefaultValue($default);
        $this->setValidator($validator);
        $this->isRequired = $isRequired;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDefaultValue($value)
    {
        $this->default = $value;
    }

    public function setValidator(?ValueValidator $validator)
    {
        if($validator === null) {
            $validator = new ValueValidator();
        }

        $this->validator = $validator;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function hasDefaultValue() : bool
    {
        return ! is_a($this->default, MissingValue::class);
    }

    public function getDefaultValue()
    {
        return $this->default;
    }

    public function getValidator() : ValueValidator
    {
        return $this->validator;
    }

    public function isRequired() : bool
    {
        return $this->isRequired;
    }

    public function isOptional() : bool
    {
        return ! $this->isRequired();
    }

    public function toArray() : array
    {
        return [
            'name' => $this->name,
            'default_value' => $this->default,
            'is_required' => $this->isRequired(),
            'validator' => $this->validator->toArray()
        ];
    }
}
