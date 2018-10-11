<?php

namespace Nonetallt\Helpers\Parameters;

class EnumParameter extends Parameter
{
    public static function getAvailableOptions()
    {
        return ['choices'];
    }

    public static function getDefaultOptions()
    {
        return ['choices' => []];
    }

    public function validateValue($value)
    {
        return in_array($value, $this->options->choices);
    }
}
