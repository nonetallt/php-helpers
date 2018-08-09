<?php

namespace Nonetallt\Helpers\Testing\Traits;

use Nonetallt\Helpers\Filesystem\Directory;

trait CleansOutput
{
    public function setUp()
    {
        $this->cleanOutput();
    }

    protected function cleanOutput(string $folder)
    {
        $folder = new Directory($folder);
        $folder->removeRecursive();
    }
}
