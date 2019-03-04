<?php

namespace Nonetallt\Helpers\Validation\Parameters\Conversion;

class ParameterTypeConversionResult
{
    private $value;
    private $errors;

    public function __construct($value = null)
    {
        $this->value = $value;
        $this->errors = [];
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function addError(string $message)
    {
        $this->errors[] = $message;
    }

    public function hasErrors()
    {
        return ! empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
