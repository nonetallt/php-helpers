<?php

namespace Nonetallt\Helpers\Filesystem\Csv;

use Nonetallt\Helpers\Generic\Container;

class Options extends Container
{
    CONST OPTIONS = [
        'delimiter',
        'enclosure',
        'escape',
        'skip_empty_lines',
        'offset_pointer',
        'offset',
        'limit',
        'header_offset',
    ];

    CONST DEFAULTS = [
        'enclosure' => '"',
        'escape' => '"',
        'skip_empty_lines' => true,
        'offset_pointer' => 0,
        'offset' => 0,
    ];

    CONST VALIDATORS = [
        'delimiter' => 'string|min:1|max:1',
        'enclosure' => 'string|min:1|max:1',
        'escape' => 'string|min:1|max:1',
        'skip_empty_lines' => 'boolean',
        'offset_pointer' => 'integer|min:0',
        'offset' => 'integer|min:0',
        'limit' => 'integer|min:0',
        'header_offset' => 'integer|min:0',
    ];

    public function __construct(array $options = [])
    {
        parent::__construct($options, self::DEFAULTS, self::VALIDATORS, self::OPTIONS);
    }
}
