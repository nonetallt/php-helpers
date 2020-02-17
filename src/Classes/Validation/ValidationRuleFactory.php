<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException;
use Nonetallt\Helpers\Validation\ValidationRuleRepository;

class ValidationRuleFactory
{
    private $ruleDelimiter;
    private $ruleParamDelimiter;
    private $paramDelimiter;
    private $reverseNotation;
    private $validatorClasses;

    public function __construct(string $ruleDelimiter = '|', string $ruleParamDelimiter = ':', string $paramDelimiter = ',')
    {
        $this->ruleDelimiter  = $ruleDelimiter;
        $this->ruleParamDelimiter  = $ruleParamDelimiter;
        $this->paramDelimiter  = $paramDelimiter;
        $this->validatorClasses = ValidationRuleRepository::getInstance();
        $this->reverseNotation = '!';
    }

    public function getRuleRepository() : ValidationRuleRepository
    {
        return $this->validatorClasses;
    }

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException
     *
     */
    public function makeRulesFromString(string $ruleList) : ValidationRuleCollection
    {
        $rules = new ValidationRuleCollection();

        /* No rules for empty string */
        if(trim($ruleList) === '') {
            return $rules;
        }

        /* Create rule from each delimited rule string */
        foreach(explode($this->ruleDelimiter, $ruleList) as $ruleString) {
             $rules->push($this->makeRuleFromString($ruleString));
        }

        return $rules;
    }

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException
     *
     */
    public function makeRuleFromString(string $ruleString) : ValidationRule
    {
        $parts = explode($this->ruleParamDelimiter, $ruleString);
        $ruleName = strtolower($parts[0]);
        $parameters = [];

        if(isset($parts[1])) {
            $parameters = explode($this->paramDelimiter, $parts[1]);
        }

        return $this->makeRule($ruleName, $parameters);
    }

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException
     *
     */
    public function makeRule(string $ruleName, array $parameters = []) : ValidationRule
    {
        $isReversed = false;

        if(starts_with($ruleName, $this->reverseNotation)) {
            $ruleName = trim(substr($ruleName, strlen($this->reverseNotation)));
            $isReversed = true;
        }

        $reflection = $this->validatorClasses[$ruleName] ?? null;

        if($reflection === null) {
            throw $this->ruleNotFound($ruleName);
        }

        $className = $reflection->name;
        return new $className($parameters, $isReversed);

    }

    public function ruleNotFound(string $name) : RuleNotFoundException
    {
        $classes = $this->validatorClasses;
        sort($classes);
        $valid = PHP_EOL . implode(PHP_EOL, $classes);
        $msg = "Rule '$name' not found in list of valid rules: $valid";
        return new RuleNotFoundException($msg);
    }
}
