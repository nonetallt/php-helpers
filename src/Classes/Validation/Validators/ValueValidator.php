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

    public function __construct(ValidationRuleCollection $rules = null)
    {
        $this->setRules($rules);
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

        foreach($this->rules as $rule) {
            $validation = $rule->validate($value, $name);

            if($validation->passed()) {
                if($validation->shouldContinue()) continue;
                else break;
            } 

            $msg = $validation->getMessage();
            $result->getExceptions()->push(new ValidationException($name, $value, $msg));
        }

        return $result;
    }

    public function getRules() : ValidationRuleCollection
    {
        return $this->rules;
    }

    public function toArray()
    {
        return [
            'rules' => $this->rules->toArray(true)
        ];
    }
}
