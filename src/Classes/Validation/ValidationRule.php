<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Validation\Parameters\ValidationRuleParameterDefinitions;
use Nonetallt\Helpers\Validation\Parameters\SimpleContainer;
use Nonetallt\Helpers\Arrays\Traits\Arrayable;
use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;
use Nonetallt\Helpers\Strings\Str;

abstract class ValidationRule
{
    use Arrayable, LazyLoadsProperties;

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

        $converter = new \CaseConverter\CaseConverter();
        $alias = $converter->convert($alias)
            ->from('camel')
            ->to('snake');

        return $alias;
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
