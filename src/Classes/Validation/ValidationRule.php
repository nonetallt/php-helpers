<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Validation\Parameters\ValidationRuleParameterDefinitions;
use Nonetallt\Helpers\Validation\Parameters\ParameterContainer;
use Nonetallt\Helpers\Arrays\Traits\Arrayable;
use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;
use Nonetallt\Helpers\Strings\Str;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;
use Jawira\CaseConverter\Convert;

abstract class ValidationRule
{
    use Arrayable, LazyLoadsProperties;

    protected $parameters;
    protected $isReversed;

    public function __construct(array $parameters = [], bool $isReversed = false)
    {
        $this->setParameters($parameters);
        $this->setReversed($isReversed);
    }

    public function setParameters(array $parameters)
    {
        if(! method_exists($this, 'defineParameters')) {
            $this->parameters = new ParameterContainer([]);
            return;
        }

        $definition = ValidationRuleParameterDefinitions::fromArray($this->defineParameters());

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
        $this->parameters = new ParameterContainer($parameters);
    }

    protected function createResult(ValidationRule $rule, bool $success, string $message, bool $continue = true)
    {
        if($this->isReversed) {
            $success = ! $success;
        }

        if($success) $message = null;
        $result = new ValidationRuleResult($rule, $message, $continue);
        return $result;
    }

    public function lazyLoadName() : string
    {
        return static::resolveName(new \ReflectionClass($this));
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public static function resolveName(\ReflectionClass $ref)
    {
        $alias = Str::removePrefix($ref->getShortName(), 'ValidationRule');
        $converter = new Convert($alias);
        return $converter->fromCamel()->toSnake();
    }

    public function setReversed(bool $reverse)
    {
        $this->isReversed = $reverse;
    }

    public function isReversed() : bool
    {
        return $this->isReversed;
    }

    public function toArray() : array
    {
        return [
            'name' => $this->getName(),
            'parameters' => $this->parameters->toArray(),
            'is_reversed' => $this->isReversed
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
