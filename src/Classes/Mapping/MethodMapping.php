<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Validation\ValidationRuleCollection;
use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleInteger;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleBoolean;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleString;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleFloat;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleArray;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleCallable;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleOptional;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleIs;
use Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException;
use Nonetallt\Helpers\Mapping\Exceptions\MappingException;

class MethodMapping extends ParameterMappingCollection
{
    CONST RULES = [
        'int' => ValidationRuleInteger::class,
        'bool' => ValidationRuleBoolean::class,
        'string' => ValidationRuleString::class,
        'float' => ValidationRuleFloat::class,
        'array' => ValidationRuleArray::class,
        'callable' => ValidationRuleCallable::class
    ];

    private $reflection;

    /**
     * @throws Nonetallt\Helpers\Mapping\Exceptions\MappingException
     */
    public function __construct(\ReflectionMethod $method)
    {
        $this->reflection = $method;
        $parameters = [];

        foreach($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $position = $parameter->getPosition();
            $default = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
            $rules = self::resolveParameterRules($parameter);
            $parameters[] = new OrderedParameterMapping($name, $position, $default, $rules);
        }

        parent::__construct($parameters);
    }

    /**
     * Find all validation rules for a given parameter
     *
     * @throws Nonetallt\Helpers\Mapping\Exceptions\MappingException
     *
     */
    public static function resolveParameterRules(\ReflectionParameter $parameter) : ValidationRuleCollection
    {
        $rules = new ValidationRuleCollection();

        if($parameter->isOptional()) {
            $rules->push(new ValidationRuleOptional());
        }

        if($parameter->hasType()) {
            try {
                $rules->push(self::resolveTypeRule($parameter));
            }
            catch(RuleNotFoundException $e) {
                $name = $parameter->getName();
                $msg = "Could not resolve validation rules for parameter '$name'";
                throw new MappingException($msg, 0, $e);
            }
        }

        return $rules;
    }

    /**
     * Find the relevant validation rule for a given parameter
     *
     * @throws Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException
     *
     */
    public static function resolveTypeRule(\ReflectionParameter $parameter) : ValidationRule
    {
        $class = $parameter->getClass();

        if($class !== null) {
            return new ValidationRuleIs([
                'class' => $class->name, 
                'accepts_string' => false
            ]);
        }

        $type = $parameter->getType();
        $ruleClass = self::RULES[(string)$type] ?? null;

        if($ruleClass === null) {
            $msg = "Validation rule for parameter type declaration '$type' could not be found";
            throw new RuleNotFoundException($msg);
        }

        return new $ruleClass;
    }

    /**
     * @throws ArgumentCountException
     * @throws TypeError
     *
     */
    public function validateMethodCall(array $parameters = [], ?\ReflectionMethod $proxy = null)
    {
        $reflection = $proxy ?? $this->reflection;

        $file = $reflection->getFileName();
        $line = $reflection->getStartLine();
        $class = $reflection->getDeclaringClass()->name;
        $expected = $this->reflection->getNumberOfRequiredParameters();
        $method = $reflection->getName();
        $passed = count($parameters);


        /* Argument count is checked first by php parser */
        if($passed < $expected) {
            $msg = "Too few arguments to function {$class}::{$method}(), $passed passed in $file on line $line and at least $expected expected";
            throw new \ArgumentCountError($msg);
        }

        /* if(! isset($this->items[$alias])) { */
        /*     $msg = $this->NotFoundException($alias); */
        /*     throw new NotFoundException($msg); */
        /* } */

        /* $expected = $mapping->getRequiredParameters()->count(); */
    }
}
