<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Validation\Parameters\ValidationRuleParameterDefinitions;
use Nonetallt\Helpers\Validation\Parameters\SimpleContainer;

abstract class ValidationRule
{
    private $name;
    protected $parameters;

    public function __construct(array $parameters = [])
    {
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
        $definition->validateValues($mappedParameters, $this->getName());

        foreach($parameters as $index => $parameter) {
            if(! is_string($index)) $unmappedParameters[] = $parameter;
        }

        $parameters = array_merge($unmappedParameters, $mappedParameters);
        $this->parameters = new SimpleContainer('validation rule parameters', $parameters);
    }

    protected function createResult(ValidationRule $rule, bool $success, string $message, bool $continue = true)
    {
        if($success) $message = null;
        $result = new ValidationRuleResult($rule, $message, $continue);
        return $result;
    }

    protected function resolveName() : string
    {
        $ref = new ValidationRuleReflection($this);
        return $ref->getAlias();
    }

    public function getName() : string
    {
        if($this->name === null) {
            $this->name = $this->resolveName();
        }

        return $this->name;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function toArray() : array
    {
        return [
            'name' => $this->name,
            'parameters' => $this->parameters->toArray()
        ];
    }

    /**
     * Validate a given value
     * 
     * @param mixed $value Value to validate
     * @param string $name Name of the value to validate
     *
     * @return ValidationRuleResult $result Validation result
     */
    abstract public function validate($value, string $name) : ValidationRuleResult;
}
