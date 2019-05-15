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

    /**
     * Validate a given value
     * 
     * @param mixed $value Value to validate
     * @param string $name Name of the value to validate
     *
     * @return ValidationResult $result Validation result
     */
    abstract public function validate($value, string $name) : ValidationResult;
}
