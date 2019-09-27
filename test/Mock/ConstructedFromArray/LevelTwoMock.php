<?php

namespace Test\Mock\ConstructedFromArray;

class LevelTwoMock extends LeveledMock
{
    public function __construct(string $name, LevelThreeMock $next)
    {
        parent::__construct($name, $next);
    }

    protected function getLevel() : int
    {
        return 2;
    }
}
