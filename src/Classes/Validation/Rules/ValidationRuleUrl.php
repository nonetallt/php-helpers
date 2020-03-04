<?php

namespace Nonetallt\Helpers\Validation\Rules;

use Nonetallt\Helpers\Validation\ValidationRule;
use Nonetallt\Helpers\Validation\Results\ValidationRuleResult;
use Nonetallt\Helpers\Strings\Str;
use Nonetallt\Helpers\Strings\Language\English;

class ValidationRuleUrl extends ValidationRule
{
    CONST FLAGS = [
        'require_path'   => FILTER_FLAG_PATH_REQUIRED,
        'require_query'  => FILTER_FLAG_QUERY_REQUIRED
    ];

    public function defineParameters()
    {
        return [
            [
                'name' => 'require_path',
                'type' => 'boolean',
                'is_required' => false
            ],
            [
                'name' => 'require_query',
                'type' => 'boolean',
                'is_required' => false
            ]
        ];
    }

    public function validate($value, string $name) : ValidationRuleResult
    {
        $flags = null;
        $flagNames = [];

        foreach($this->parameters as $parameterKey => $parameterValue) {
            if($parameterValue) {
                $flag = static::FLAGS[$parameterKey];
                $flags = $flags === null ? $flag : $flags | $flag;
                $flagNames[] = Str::removePrefix($parameterKey, 'require_');
            }
        }

        $result = filter_var($value, FILTER_VALIDATE_URL, $flags) !== false;
        $msg = "Value $name must be a valid url";

        if(count($flagNames) > 0) {
            $items = English::listWords(...$flagNames);
            $msg .= " with $items";
        }

        return $this->createResult($this, $result, $msg);
    }
}
