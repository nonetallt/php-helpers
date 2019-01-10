<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Validation\Parameters\ValidationRuleParameterDefinitions;
use Nonetallt\Helpers\Validation\Parameters\SimpleContainer;

abstract class ValidationRule
{
    protected $name;
    protected $parameters;

    public function __construct(string $name, array $parameters)
    {
        $this->name = $name;

        $definition = new ValidationRuleParameterDefinitions([]);

        if(method_exists($this, 'defineParameters')) {
            $definition = ValidationRuleParameterDefinitions::fromArray($this->defineParameters());
        }

        /* Maps values from array to parameter definition, given parameters can
           be keyed by index order or associative array where key name is
           the parameter name 
         */
        $unmappedParameters = [];
        $mappedParameters = $definition->mapValues($parameters);
        $definition->validateValues($mappedParameters, $name);

        foreach($parameters as $index => $parameter) {
            if(! is_string($index)) $unmappedParameters[] = $parameter;
        }

        $parameters = array_merge($unmappedParameters, $mappedParameters);
        $this->parameters = new SimpleContainer('validation rule parameters', $parameters);
    }

    /**
     * Returned value must be an instance of ValidationResult or an Exception
     * will be thrown.
     * 
     * @param mixed $value Value to validate
     * @param string $name Name of the value to validate
     *
     * @return ValidationResult $result Validation result
     */
    protected abstract function validateValue($value, string $name);

    public function validate($value, string $name)
    {
        $result = $this->validateValue($value, $name);
        if(is_a($result, ValidationResult::class)) return $result;

        $actual = gettype($result);
        if($actual === 'object') $actual = get_class($actual);
        $expected = ValidationResult::class;

        throw new \Exception("Method ValidateValue() of child class returned $actual instead of expected $expected");
    }  

    protected function createResult(ValidationRule $rule, bool $success, string $message)
    {
        if($success) $message = null;
        $result = new ValidationResult($rule, $message);
        return $result;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
