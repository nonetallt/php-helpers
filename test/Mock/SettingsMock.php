<?php

namespace Test\Mock;

use Nonetallt\Helpers\Common\Settings;

class SettingsMock extends Settings
{
    protected static function defineSettings() : array
    {
        return [
            [
                'name' => 'foo',
                'validate' => 'string'
            ],
            [
                'name' => 'bar',
                'default' => 'baz',
                'validate' => 'string'
            ],
            [
                'name' => 'baz',
                'validate' => 'integer'
            ]
        ];
    }
}
