<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;
use Nonetallt\Helpers\Validation\ValidationRuleCollection;
use Nonetallt\Helpers\Validation\Results\ValidationResult;

/**
 * Validator that can validate a single value against a set of rules
 *
 * This class acts as a functionality container for rule collections
 *
 */
class ValueValidator
{
    private $rules;
    private $prependedRules;
    private $appendedRules;

    public function __construct(ValidationRuleCollection $rules = null)
    {
        $this->setRules($rules);
        $this->prependedRules = null;
        $this->appendedRules = null;
    }

    public function setRules(?ValidationRuleCollection $rules)
    {
        if($rules === null) $rules = new ValidationRuleCollection();
        $this->rules = $rules;
    }

    /**
     * Validate a single value
     *
     * @param string $name Name of the value being validated, used for display
     * purposes if validation fails
     *
     * @param mixed $value Value to validate
     *
     * @return Nonetallt\Helpers\Validation\Results\ValidationResult $result 
     *
     */
    public function validate(string $name, $value) : ValidationResult
    {
        $result = new ValidationResult();
        
        foreach($this->getEffectiveRules() as $rule) {
            $ruleResult = $rule->validate($value, $name);

            if($ruleResult->failed()) {
                $msg = $ruleResult->getErrorMessage();
                $result->getExceptions()->push(new ValidationException($name, $value, $msg));
            }

            if(! $ruleResult->shouldContinue()) {
                break;
            }
        }

        $this->prependedRules = null;
        $this->appendedRules = null;

        return $result;
    }

    /**
     * Run the given rules before other validation rules for the next
     * validation only
     *
     */
    public function prependRules(ValidationRuleCollection $rules)
    {
        $this->prependedRules = $rules;
        return $this;
    }

    /**
     * Rune the given rules after other validation rules for the next
     * validation only
     *
     */
    public function appendRules(ValidationRuleCollection $rules)
    {
        $this->appendedRules = $rules;
        return $this;
    }

    public function getRules() : ValidationRuleCollection
    {
        return $this->rules;
    }

    public function getEffectiveRules() : ValidationRuleCollection
    {
        $rules = new ValidationRuleCollection();

        if($this->prependedRules !== null) {
            $rules->pushAll($this->prependedRules);
        }

        $rules->pushAll($this->rules);

        if($this->appendedRules !== null) {
            $rules->pushAll($this->appendedRules);
        }

        return $rules;
    }

    public function toArray()
    {
        return [
            'rules' => $this->rules->toArray(true)
        ];
    }
}
