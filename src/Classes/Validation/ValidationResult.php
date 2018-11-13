<?php

namespace Nonetallt\Helpers\Validation;

class ValidationResult
{
    private $validationRule;
    private $message;

    public function __construct(ValidationRule $validationRule, string $message = null)
    {
        $this->validationRule = $validationRule;
        $this->message = $message;
    }

    public function passed()
    {
        return is_null($this->message);
    }

    public function failed()
    {
        return ! is_null($this->message);
    }

    public function getValidationRule()
    {
        return $this->validationRule;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
