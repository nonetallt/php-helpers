<?php

namespace Nonetallt\Helpers\Validation\Parameters;

class ParameterValidator
{
    private $rules;

    public function __construct()
    {
        $this->rules =  [
            'string' => function($value) {
                return is_string($value);
            },
            'integer' => function($value) {
                return is_integer($value);
            },
            'numeric' => function($value) {
                return is_numeric($value);
            },
            'scalar' => function($value) {
                return is_scalar($value);
            },
            'bool' => function($value) {
                return is_bool($value);
            },
        ]; 
    }

    public function validateType(string $type)
    {
        return required_in_array($type, $this->validTypes());
    }

    public function validTypes()
    {
        return array_keys($this->rules);
    }

    public function validate(string $type, $value)
    {
        if(! isset($this->rules[$type])) {
            throw new \Exception("Cannot validate type $type, no callback available");
        }
            
        return $this->rules[$type]($value);
    }
}
