<?php

namespace Nonetallt\Helpers\Validation\Parameters;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Validation\Parameters\Conversion\ParameterConversionFactory;
use Nonetallt\Helpers\Validation\Parameters\Conversion\ParameterTypeConversionException;

/**
 * Simple validation for parameters given for validation rules.  
 * 
 */
class ValidationRuleParameterDefinitions
{
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = TypedArray::create(ValidationRuleParameterDefinition::class, $parameters);
    }

    /** 
     * Maps values from array to parameter definition, given parameters can be
     * keyed by index order or associative array where key name is parameter name 
     */
    public function mapValues(array $values)
    {
        $errors = [];
        $mapped = [];

        foreach($this->parameters as $param) {
            $name = $param->getName();
            $position = $param->getPosition();
            $type = $param->getType();
            $value = $values[$name] ?? $values[$position -1] ?? null;

            /* Try converting parameter value to the correct type */
            try {
                $result = ParameterConversionFactory::convertToType($value, $type);
                $value = $result->getValue();
            }
            catch(ParameterTypeConversionException $e) {
                $errors[] = $e->getMessage();
            }

            $mapped[$name] = $value;
        }

        /* Display errors if there are any */
        if(! empty($errors)) throw new \Exception(implode(PHP_EOL, $errors));

        return $mapped;
    }

    public function validateValues(array $values, string $ruleName)
    {
        $validator = new ParameterValidator();
        $currentParameter = 0;
        $errors = [];

        foreach($this->parameters as $parameter) {

            $currentParameter++;
            $name = $parameter->getName();

            /* Parameter missing */
            if(! isset($values[$name])) {
                /* Create error if missing parameter is required */
                if($parameter->isRequired()) $errors[$name][] = "Parameter $currentParameter ($name) is required for rule $ruleName";
                continue;
            }
            
            $value = $values[$name];
            $type = $parameter->getType();

            if(! $validator->validate($type, $value)) {
                $given = gettype($value);
                $errors[$name][] = "Parameter $currentParameter ($name) for rule $ruleName is of incorrect type $given, expected $type";
            }
        }

        if(! empty($errors)) {
            $msg = '';
            foreach($errors as $field => $messages) {
                /* Add separator if message has content already */
                if($msg !== '') $msg .= PHP_EOL;
                $msg .= implode(PHP_EOL, $messages);
            }
            throw new \Exception($msg);
        }

        return true;
    }

    public static function fromArray(array $params)
    {
        $parameters = [];
        $position = 1;

        foreach($params as $param) {
            $parameters[] = self::createDefinition($position, $param);
            $position++;
        }

        return new self($parameters);
    }

    private static function createDefinition(int $position, $param)
    {
        if(! is_array($param)) {
            $type = gettype($param);
            $msg = "Can't create ValidationRuleParameter from $type, array expected";
            throw new \InvalidArgumentException($msg);
        }

        /* All array keys required to construct validation rule parameter */
        $required = ValidationRuleParameterDefinition::REQUIRED_KEYS;
        $missing = array_keys_missing($required, $param);

        /* Make sure that array has all the required keys */
        if(! empty($missing)) {
            $msg = "Missing required array keys for ValidationRuleParameterDefinition:".PHP_EOL.implode(PHP_EOL, $missing);
            throw new \InvalidArgumentException($msg);
        }

        return new ValidationRuleParameterDefinition($position, $param['name'], $param['is_required'], $param['type']);
    }
}
