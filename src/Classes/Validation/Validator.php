<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Arrays\TypedArray;

class Validator
{
    private $rules;
    private $rulesStrings;
    private $errors;
    private $factory;

    public function __construct(array $rules)
    {
        $ruleDelimiter      = '|';
        $ruleParamDelimiter = ':';
        $paramDelimiter     = ',';

        $this->ruleStrings = TypedArray::create('string', $rules);
        $this->factory = new ValidationRuleFactory($ruleDelimiter, $ruleParamDelimiter, $paramDelimiter);
        $this->errors = [];
    }

    public function validate(array $data)
    {
        $this->errors = [];

        foreach($data as $key => $value) {
            foreach($this->getRulesFor($key) as $rule) {
                $result = $rule->validate($value, $key);
                if($result->passed()) continue;
                $this->errors[$key][] = $result->getMessage();
            }
        }

        if(empty($this->errors)) return true;
        return false;
    }

    private function resolveRules()
    {
        $this->rules = [];
        foreach($this->ruleStrings as $key => $string) {
            $this->rules[$key] = $this->factory->makeRulesFromString($string);
        }
    }

    public function passes(array $data)
    {
        return $this->validate($data) === true;
    }

    public function fails(array $data)
    {
        return $this->validate($data) === false;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

    public function getRuleStrings() : array
    {
        return $this->ruleStrings;
    }

    public function getAllRules() : array
    {
        if($this->rules === null) $this->resolveRules(); 
        return $this->rules;
    }

    public function getFieldNames() : array
    {
        return array_keys($this->ruleStrings);
    }

    public function getRulesFor(string $fieldName) : ValidationRuleCollection
    {
        foreach($this->getAllRules() as $field => $rules) {
            if($field === $fieldName) return $rules;
        }
        return new ValidationRuleCollection();
    }
}
