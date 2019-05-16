<?php

namespace Nonetallt\Helpers\Mapping;

use Nonetallt\Helpers\Validation\ValidationRuleCollection;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;

class ParameterMapping
{
    private $name;
    private $default;
    private $rules;

    public function __construct(string $name, $default = null, ValidationRuleCollection $rules = null)
    {
        $this->setName($name);
        $this->setDefaultValue($default);
        $this->setValidationRules($rules);
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setDefaultValue($value)
    {
        $this->default = $value;
    }

    public function setValidationRules(?ValidationRuleCollection $rules)
    {
        if($rules === null) {
            $rules = new ValidationRuleCollection();
        }

        $this->rules = $rules;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getDefaultValue()
    {
        return $this->default;
    }

    public function isRequired() : bool
    {
        return $this->default === null;
    }

    public function getValidationRules() : ValidationRuleCollection
    {
        return $this->rules;
    }

    /**
     * @throws Nonetallt\Helpers\Validation\Exceptions\ValidationException
     */
    public function validateValue($value)
    {
        $errors = [];

        foreach($this->rules as $rule) {
            $validation = $rule->validate($value, $this->name);

            if($validation->passed()) {
                if($validation->shouldStop()) break;
                else continue;
            }

            $errors[$this->name][] = $validation->getMessage();
        }

        if(! empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function toArray() : array
    {
        return [
            'name' => $this->name,
            'default_value' => $this->default,
            'is_required' => $this->isRequired(),
            'validation_rules' => $this->rules->serializeToArray()
        ];
    }
}
