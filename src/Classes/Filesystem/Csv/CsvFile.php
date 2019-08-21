<?php

namespace Nonetallt\Helpers\Filesystem\Csv;

use Nonetallt\Helpers\Filesystem\GenericFile;

class CsvFile extends GenericFile
{
    public function __construct(string $path)
    {
        parent::__cosntruct($path);
    }
}
