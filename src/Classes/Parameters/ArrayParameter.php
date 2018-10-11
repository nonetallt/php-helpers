<?php

namespace Nonetallt\Helpers\Parameters;

class ArrayParameter extends Parameter
{
    public static function getAvailableOptions()
    {
        return ['required_keys'];
    }

    public static function getDefaultOptions()
    {
        return ['required_keys' => []];
    }

    public function validateValue($value)
    {
        if(! is_array($value)) return false;

        foreach($this->options->required_keys as $required) {
            if(! isset($required)) return false;
        }

        return true;
    }
}
