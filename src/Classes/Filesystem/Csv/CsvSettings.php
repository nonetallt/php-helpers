<?php

namespace Nonetallt\Helpers\Filesystem\Csv;

use Nonetallt\Helpers\Common\Settings;

class CsvSettings extends Settings
{
    protected static function defineSettings() : array
    {
        return [
            [
                'name' => 'delimiter',
                'validate' => 'string|min:1|max:1' 
            ],
            [
                'name' => 'enclosure',
                'default' => '"',
                'validate' => 'string|min:1|max:1' 
            ],
            [
                'name' => 'escape',
                'default' => '"',
                'validate' => 'string|min:1|max:1' 
            ],
            [
                'name' => 'skip_empty_lines',
                'default' => true,
                'validate' => 'boolean' 
            ],
            [
                'name' => 'offset_pointer',
                'default' => 0,
                'validate' => 'integer|min:0' 
            ],
            [
                'name' => 'offset',
                'default' => 0,
                'validate' => 'integer|min:0' 
            ],
            [
                'name' => 'limit',
                'validate' => 'integer|min:0' 
            ],
            [
                'name' => 'header_offset',
                'validate' => 'integer|min:0' 
            ],
        ];
    }
}
