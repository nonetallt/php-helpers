<?php

namespace Test\Mock\ConstructedFromArray;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

abstract class LeveledMock
{
    use ConstructedFromArray;

    private $name;
    private $level;
    private $next;

    public function __construct(string $name, LeveledMock $next = null)
    {
        $this->name = $name;
        $this->level = $this->getLevel();
        $this->next = $next;
    }

    abstract protected function getLevel() : int;

    public function toArray() : array 
    {
        $next = $this->next !== null ? $this->next->toArray() : null;

        return [
            'name' => $this->name,
            'level' => $this->level,
            'next' => $next
        ];
    }
}
