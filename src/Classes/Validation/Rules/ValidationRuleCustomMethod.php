<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\ValidationRuleResult;
use Nonetallt\Helpers\Describe\DescribeObject;

class ValidationRuleCustomMethod extends ValidationRule
{

    public function defineParameters()
    {
        return [
            [
                'name' => 'class',
                'type' => 'string',
                'is_required' => true
            ],
            [
                'name' => 'method',
                'type' => 'string',
                'is_required' => true
            ]
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $class = $this->parameters->class;
        $method = $this->parameters->method;
        $ref = new \ReflectionClass($class);

        $signature = "$class::$method()";

        /* Make sure method exists */
        if(! $ref->hasMethod($method)) {
            $msg = "Cannot use custom validation method $signature, method does not exist";
            throw new \Exception($msg);
        }

        $methodRef = $ref->getMethod($method);

        /* Make sure method is static */
        if(! $methodRef->isStatic()) {
            $msg = "Cannot use custom validation method $signature, method must be static";
            throw new \Exception($msg);
        }

        /* Make sure the method signature matches */
        $this->validateCallbackParameters($methodRef->getParameters(), $signature);

        $result = $class::$method($value, $name, function($success, $message) {
            return $this->createResult($this, $success, $message);
        });

        /* Make sure the method returns correct type */
        $expected = ValidationRuleResult::class;
        if(! is_a($result, $expected)) {
            
            $actual = (new DescribeObject($result))->describeType();
            $msg = "Custom validation method $signature return type was $actual instead of expected $expected, make sure the callback value is returned";
            throw new \Exception($msg);
        }

        return $result;
    }

    /**
     * Use reflection parameters to make sure the callback can be called
     * succesfully
     */
    private function validateCallbackParameters(array $params, string $signature)
    {
        $required = [
            0 => '',
            1 => 'string',
            2 => 'callable'
        ];

        $errors = [];

        foreach($required as $index => $expectedType) {

            $position = $index + 1;
            $declaration = "$expectedType or no declaration";
            if($expectedType === '') $declaration = "no declaration";

            if(! isset($params[$index])) {
                $errors[] = "Missing required argument for position $position ($declaration)";
                continue;
            }

            $param = $params[$index];
            $type = (string)$param->getType();

            if($type !== $expectedType && $type !== null) {
                $errors[] = "Argument $position for method $signature should be declared one of the following: ($declaration), declared $type instead";
            }
            
        }

        if(! empty($errors)) {
            throw new \Exception(implode(PHP_EOL, $errors));
        }
    }
}
