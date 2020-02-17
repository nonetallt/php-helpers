<?php

namespace Nonetallt\Helpers\Validation\Validators;

use Nonetallt\Helpers\Validation\Results\ValidationResult;

interface ValidatorInterface
{
    public function validate() : ValidationResult
}
