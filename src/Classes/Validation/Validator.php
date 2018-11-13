<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Arrays\TypedArray;

class Validator
{
    private $data;
    private $rules;
    private $errors;

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = TypedArray::create('string', $rules);
        $this->errors = [];
    }

    public function validate()
    {
        $ruleDelimiter      = '|';
        $ruleParamDelimiter = ':';
        $paramDelimiter     = ',';

        $this->errors = [];
        $factory = new ValidationRuleFactory($ruleDelimiter, $ruleParamDelimiter, $paramDelimiter);

        foreach($this->data as $key => $value) {
            $ruleList = $this->rules[$key] ?? [];
            $rules = $factory->makeRules($ruleList);

            foreach($rules as $rule) {
                $result = $rule->validate($value);
                if($result->passed()) continue;
                $errors[$key][] = $result->getMessage();
            }
        }

        $this->errors = $errors;

        if(empty($errors)) return true;
        return false;
    }

    public function passes()
    {
        return $this->validate() === true;
    }

    public function fails()
    {
        return $this->validate() === false;
    }

    public function getErrors()
    {
        return $this->errors();
    }

    public function getRules()
    {
        return $this->rules;
    }
}
