<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Arrays\TypedArray;

class Validator
{
    private $rules;
    private $errors;

    public function __construct(array $rules)
    {
        $this->rules = TypedArray::create('string', $rules);
        $this->errors = [];
    }

    public function validate(array $data)
    {
        $ruleDelimiter      = '|';
        $ruleParamDelimiter = ':';
        $paramDelimiter     = ',';

        $this->errors = [];
        $factory = new ValidationRuleFactory($ruleDelimiter, $ruleParamDelimiter, $paramDelimiter);

        foreach($data as $key => $value) {
            $ruleList = $this->rules[$key] ?? [];
            $rules = $factory->makeRules($ruleList);

            foreach($rules as $rule) {
                $result = $rule->validate($value, $key);
                if($result->passed()) continue;
                $this->errors[$key][] = $result->getMessage();
            }
        }

        if(empty($this->errors)) return true;
        return false;
    }

    public function passes(array $data)
    {
        return $this->validate($data) === true;
    }

    public function fails(array $data)
    {
        return $this->validate($data) === false;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getRules()
    {
        return $this->rules;
    }
}
