<?php

namespace Nonetallt\Helpers\Validation\Parameters;

use Nonetallt\Helpers\Validation\Parameters\ParameterValidator;

/**
 * Simple validation for parameters given for validation rules.  
 * 
 */
class ValidationRuleParameterDefinition
{
    const REQUIRED_KEYS = ['name', 'is_required', 'type'];

    private $position;
    private $name;
    private $isRequired;
    private $type;

    public function __construct(int $position, string $name, bool $isRequired, string $type)
    {
        $validator = new ParameterValidator();
        $validator->validateType($type);

        $this->position = $position;
        $this->name = $name;
        $this->isRequired = $isRequired;
        $this->type = $type;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isRequired()
    {
        return $this->isRequired;
    }

    public function getType()
    {
        return $this->type;
    }
}
