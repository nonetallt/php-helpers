<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException;

/**
 * TODO split validator classes into ValidationRuleRepository singleton ?
 */
class ValidationRuleFactory
{
    use FindsReflectionClasses;

    private $ruleDelimiter;
    private $ruleParamDelimiter;
    private $paramDelimiter;
    private $validatorClasses;

    public function __construct(string $ruleDelimiter = '|', string $ruleParamDelimiter = ':', string $paramDelimiter = ',')
    {
        $this->ruleDelimiter  = $ruleDelimiter;
        $this->ruleParamDelimiter  = $ruleParamDelimiter;
        $this->paramDelimiter  = $paramDelimiter;

        $namespace = __NAMESPACE__ . '\\Rules';
        $directory = __DIR__ . '/Rules';

        $this->validatorClasses = $this->findReflectionClasses($namespace, $directory, ValidationRule::class);
    }

    /**
     * Trait method, customize class
     *
     * @override
     */
    protected function createReflectionClass(string $class) : \ReflectionClass
    {
        return new ValidationRuleReflection($class);
    }

    /**
     * Returns an array with rule aliases as keys and fully qualified class
     * names as values.
     *
     * @return array $mapping
     */
    public function validationRuleMapping()
    {
        $mapping = [];
        foreach($this->validatorClasses as $ref) {
            $mapping[$ref->getAlias()] = $ref->name;
        }

        return $mapping;
    }

    public function validatorsAvailable()
    {
        return array_map(function($reflection) {
            return (string)$reflection;
        }, $this->validatorClasses);
    }

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException
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
     */
    public function makeRule(string $ruleName, array $parameters = []) : ValidationRule
    {
        foreach($this->validatorClasses as $class) {
            if($ruleName !== $class->getAlias()) continue;
            $className = $class->name;
            return new $className($parameters);
        }

        throw $this->ruleNotFound($ruleName);
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
