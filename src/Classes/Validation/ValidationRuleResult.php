<?php

namespace Nonetallt\Helpers\Validation;

class ValidationRuleResult
{
    private $validationRule;
    private $message;
    private $continue;

    public function __construct(ValidationRule $validationRule, ?string $message = null, bool $continue = true)
    {
        $this->validationRule = $validationRule;
        $this->message = $message;
        $this->continue = $continue;
    }

    public function passed() : bool
    {
        return is_null($this->message);
    }

    public function failed() : bool
    {
        return ! is_null($this->message);
    }

    /**
     * Wether validatin should be stopped
     */
    public function shouldStop() : bool
    {
        return ! $this->continue;
    }

    /**
     * Wether validatin should be continued
     */
    public function shouldContinue() : bool
    {
        return $this->continue;
    }

    public function getValidationRule() : ValidationRule
    {
        return $this->validationRule;
    }

    public function getMessage() : string
    {
        return $this->message;
    }
}
