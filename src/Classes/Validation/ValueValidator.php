<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Arrays\TypedArray;
use Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;

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
     * @return Nonetallt\Helpers\Validation\Exceptions\ValidationExceptionCollection $exceptions
     * All validation exceptions encountered
     *
     */
    public function validate(string $name, $value) : ValidationExceptionCollection
    {
        $exceptions = new ValidationExceptionCollection();

        foreach($this->rules as $rule) {
            $validation = $rule->validate($value, $name);

            if($validation->passed()) {
                if($validation->shouldContinue()) continue;
                else break;
            } 

            $msg = $validation->getMessage();
            $exception = new ValidationException($name, $value, $msg);
            $exceptions->push($exception);
        }

        return $exceptions;
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
