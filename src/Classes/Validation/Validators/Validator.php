<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Validation\ValidationRuleFactory;
use Nonetallt\Helpers\Validation\ValidationRuleCollection;
use Nonetallt\Helpers\Validation\Results\ValidationResult;
use Nonetallt\Helpers\Describe\DescribeObject;

class Validator
{
    private $factory;
    private $ruleStrings;
    private $valueValidators;

    public function __construct(array $rules)
    {
        $ruleDelimiter      = '|';
        $ruleParamDelimiter = ':';
        $paramDelimiter     = ',';

        $this->valueValidators = [];
        $this->setRuleStrings($rules);
        $this->factory = new ValidationRuleFactory($ruleDelimiter, $ruleParamDelimiter, $paramDelimiter);
    }

    public function setRuleStrings(array $rules)
    {
        foreach($rules as $rule) {
            if(! is_string($rule)) {
                $type = (new DescribeObject($rule))->describeType();
                $msg = "Given rules must be strings, $type given";
                throw new \InvalidArgumentException($msg);
            }
        }

        $this->ruleStrings = $rules;
    }

    public function validate(array $data) : ValidationResult
    {
        $result = new ValidationResult();

        foreach($data as $key => $value) {
            $exceptions = $this->validateValue($key, $value)->getExceptions();
            $result->getExceptions()->pushAll($exceptions);
        }

        return $result;
    }

    public function validateValue(string $key, $value) : ValidationResult
    {
        $validator = $this->getValueValidator($key);
        return $validator->validate($key, $value);
    }

    public function getValueValidator(string $key) : ValueValidator
    {
        if(! isset($this->valueValidators[$key])) {
            $this->valueValidators[$key] = $this->resolveValidator($key);
        }

        return $this->valueValidators[$key];
    }

    private function resolveValidator(string $key) : ValueValidator
    {
        $string = $this->ruleStrings[$key] ?? '';
        return new ValueValidator($this->factory->makeRulesFromString($string));
    }

    public function hasValidatorFor(string $key) : bool
    {
        return isset($this->ruleStrings[$key]);
    }

    public function getRuleStrings() : array
    {
        return $this->ruleStrings;
    }

    public function getValidatedFields() : array
    {
        return array_keys($this->ruleStrings);
    }
}
