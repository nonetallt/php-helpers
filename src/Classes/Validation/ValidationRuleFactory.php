<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException;
use Nonetallt\Helpers\Validation\ValidationRuleRepository;

class ValidationRuleFactory
{
    private $settings;
    private $ruleRepository;

    public function __construct(ValidationRuleRepository $repo = null, ValidationRuleParsingSettings $settings = null)
    {
        $this->ruleRepository = $repo ?? new ValidationRuleRepository();
        $this->settings = $settings ?? new ValidationRuleParsingSettings();
    }

    public function getRuleRepository() : ValidationRuleRepository
    {
        return $this->ruleRepository;
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
        foreach(explode($this->settings->rule_delimiter, $ruleList) as $ruleString) {
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
        $parts = explode($this->settings->rule_parameter_delimiter, $ruleString);
        $ruleName = strtolower($parts[0]);
        $parameters = [];

        if(isset($parts[1])) {
            $parameters = explode($this->settings->parameter_delimiter, $parts[1]);
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

        if(starts_with($ruleName, $this->settings->reverse_notation)) {
            $ruleName = trim(substr($ruleName, strlen($this->settings->reverse_notation)));
            $isReversed = true;
        }

        $reflection = $this->ruleRepository[$ruleName] ?? null;

        if($reflection === null) {
            throw $this->ruleNotFound($ruleName);
        }

        $className = $reflection->name;
        return new $className($parameters, $isReversed);

    }

    public function ruleNotFound(string $name) : RuleNotFoundException
    {
        $classes = $this->ruleRepository;
        sort($classes);
        $valid = PHP_EOL . implode(PHP_EOL, $classes);
        $msg = "Rule '$name' not found in list of valid rules: $valid";
        return new RuleNotFoundException($msg);
    }
}
