<?php

namespace Test\Mock\ConstructedFromArray;

class LevelThreeMock extends LeveledMock
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    protected function getLevel() : int
    {
        return 3;
    }
}
