<?php

namespace Nonetallt\Helpers\Validation;

abstract class ValidationRule
{
    private $name;
    private $params;

    public function __construct(string $name, array $params)
    {
        $this->name = $name;
        $this->params = $params;
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

        throw new \Exception("Validation returned $actual instead of expected $expected");
    }  

    protected function createResult(ValidationRule $rule, bool $success, string $message)
    {
        if($success) $message = null;
        $result = new ValidationResult($rule, $message);
        return $result;
    }

    public function getParameter(int $index)
    {
        $value = $this->params[$index] ?? null;
        return $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParams()
    {
        return $this->params;
    }
}
