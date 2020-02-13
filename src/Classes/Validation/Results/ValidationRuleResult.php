<?php

namespace Nonetallt\Helpers\Validation\Results;

use Nonetallt\Helpers\Validation\ValidationRule;

class ValidationRuleResult
{
    private $validationRule;
    private $error;
    private $continue;

    public function __construct(ValidationRule $validationRule, ?string $error = null, bool $continue = true)
    {
        $this->validationRule = $validationRule;
        $this->error = $error;
        $this->continue = $continue;
    }

    /**
     * Whether validation passed
     *
     */
    public function passed() : bool
    {
        return $this->error === null;
    }

    /**
     * Whether validation failed
     *
     */
    public function failed() : bool
    {
        return $this->error !== null;
    }

    /**
     * Whether validation should be stopped
     *
     * @return bool $shouldStop
     *
     */
    public function shouldStop() : bool
    {
        return ! $this->continue;
    }

    /**
     * Whether validatin should be continued
     *
     * @return bool $shouldContinue
     *
     */
    public function shouldContinue() : bool
    {
        return $this->continue;
    }

    /**
     * Get the original validation rule
     *
     */
    public function getValidationRule() : ValidationRule
    {
        return $this->validationRule;
    }

    /**
     * Get the error message string or null if validation was success
     *
     */
    public function getErrorMessage() : ?string
    {
        return $this->error;
    }
}
