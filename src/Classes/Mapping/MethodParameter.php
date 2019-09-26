<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Validation\ValueValidator;
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
use Nonetallt\Helpers\Validation\Rules\ValidationRuleObject;

class MethodParameter extends OrderedParameterMapping
{
    CONST RULES = [
        'int' => ValidationRuleInteger::class,
        'bool' => ValidationRuleBoolean::class,
        'string' => ValidationRuleString::class,
        'float' => ValidationRuleFloat::class,
        'array' => ValidationRuleArray::class,
        'callable' => ValidationRuleCallable::class,
        'iteratable' => ValidationRuleIterable::class,
        'object' => ValidationRuleObject::class
    ];

    private $reflection;
    private $type;

    public function __construct(\ReflectionParameter $reflection)
    {
        $this->reflection = $reflection;

        $name = $reflection->getName();
        $position = $reflection->getPosition();
        $default = $reflection->isDefaultValueAvailable() ? $reflection->getDefaultValue() : null;
        $validator = new ValueValidator(self::resolveParameterRules($reflection));
        $isRequired  = ! $reflection->isOptional();
        $type = (string)$reflection->getType();

        $this->type = $type;
        parent::__construct($name, $position, $default, $validator, $isRequired);
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

        if($parameter->allowsNull()) {
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
    private static function resolveTypeRule(\ReflectionParameter $parameter) : ValidationRule
    {
        $class = $parameter->getClass();

        /* If parameter has class use class rule. This also catches "self" declaration */
        if($class !== null) {
            return new ValidationRuleIs([
                'class' => $class->name, 
                'accepts_string' => false
            ]);
        }

        $type = $parameter->getType()->getName();
        $ruleClass = self::RULES[(string)$type] ?? null;

        if($ruleClass === null) {
            $msg = "Validation rule for parameter type declaration '$type' could not be found";
            throw new RuleNotFoundException($msg);
        }

        return new $ruleClass;
    }

    public function getReflection() : \ReflectionParameter
    {
        return $this->reflection;
    }

    public function setType(?string $type)
    {
        $this->type = $type;
    }

    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @override
     */
    public function toArray() : array
    {
        $array = parent::toArray();
        $array['type'] = $this->type;
        return $array;
    }
}
