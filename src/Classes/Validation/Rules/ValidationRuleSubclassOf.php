<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleSubclassOf extends ValidationRule
{
    public function defineParameters()
    {
        return [
            [
                'name' => 'parent_class',
                'type' => 'string',
                'is_required' => true
            ],
            [
                'name' => 'accepts_string',
                'type' => 'boolean',
                'is_required' => false
            ],
        ];
    }

    public function validateValue($value, string $name)
    {
        $parent = $this->parameters->parent_class;
        $acceptString = $this->parameters->accepts_string ?? false;

        return $this->createResult($this, is_subclass_of($value, $parent, $acceptString), "Value $name must be a subclass of $parent");
    }
}
