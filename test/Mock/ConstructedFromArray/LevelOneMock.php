<?php

namespace Test\Mock\ConstructedFromArray;

class LevelOneMock extends LeveledMock
{
    public function __construct(string $name, LevelTwoMock $next)
    {
        parent::__construct($name, $next);
    }

    protected function getLevel() : int
    {
        return 1;
    }
}
