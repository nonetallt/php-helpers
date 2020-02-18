<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Common\Settings;

class ValidationRuleParsingSettings extends Settings
{
    protected static function defineSettings() : array
    {
        /* NOTE do not validate validator settings or infine loop will occur */
        return [
            [
                'name' => 'rule_delimiter',
                'default' => '|',
                /* 'validate' => 'string|min:1' */
            ],
            [
                'name' => 'rule_parameter_delimiter',
                'default' => ':',
                /* 'validate' => 'string|min:1' */
            ],
            [
                'name' => 'parameter_delimiter',
                'default' => ',',
                /* 'validate' => 'string|min:1' */
            ],
            [
                'name' => 'reverse_notation',
                'default' => '!',
                /* 'validate' => 'string|min:1' */
            ],
        ];
    }
}
