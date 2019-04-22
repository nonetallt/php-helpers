<?php

namespace Nonetallt\Helpers\Testing\Traits;

trait TestsFiles
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
