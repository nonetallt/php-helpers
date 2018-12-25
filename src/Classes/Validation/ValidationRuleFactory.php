<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;

class ValidationRuleFactory
{
    use FindsReflectionClasses;

    private $ruleDelimiter;
    private $ruleParamDelimiter;
    private $paramDelimiter;
    private $validatorClasses;

    public function __construct(string $ruleDelimiter, string $ruleParamDelimiter, string $paramDelimiter)
    {
        $this->ruleDelimiter  = $ruleDelimiter;
        $this->ruleParamDelimiter  = $ruleParamDelimiter;
        $this->paramDelimiter  = $paramDelimiter;

        $namespace = __NAMESPACE__ . '\\Rules';
        $directory = __DIR__ . '/Rules';

        $classes = $this->findReflectionClasses($namespace, $directory, ValidationRule::class);
        $this->validatorClasses = array_map(function($reflectionClass) {
            return new ValidationRuleReflection($reflectionClass);
        }, $classes);
    }

    public function validatorsAvailable()
    {
        return array_map(function($reflection) {
            return (string)$reflection;
        }, $this->validatorClasses);
    }

    public function makeRules(string $ruleList)
    {
        if(trim($ruleList) === '') return [];

        $ruleStrings = explode($this->ruleDelimiter, $ruleList);

        /* if there is no splitter, consider string as a single rule */
        if($ruleStrings === false) $ruleStrings = [$ruleList];

        $parsedRules = [];

        foreach($ruleStrings as $ruleString) {
            $parsedRules[] = $this->parseRule($ruleString);
        }

        return $parsedRules;
    }

    private function parseRule(string $ruleString)
    {
        $parts = explode($this->ruleParamDelimiter, $ruleString);
        $name = strtolower($parts[0]);
        $params = [];

        if(isset($parts[1])) $params = explode($this->paramDelimiter, $parts[1]);

        foreach($this->validatorClasses as $class) {
            if($name === $class->getAlias()) {
                $className = $class->getFullName();
                return new $className($name, $params);
            }
        }

        $classes = $this->validatorClasses;
        sort($classes);

        $valid = PHP_EOL . implode(PHP_EOL, $classes);
        $msg = "Rule '$name' not found in list of valid rules: $valid";
        throw new \Exception($msg);
    }
}
