<?php

namespace Nonetallt\Helpers\Templating;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

class Position
{
    use ConstructedFromArray;

    private $type;
    private $position;

    public function __construct(string $type, int $position)
    {
        $this->type = $type;
        $this->position = $position;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPosition()
    {
        return $this->position;
    }
}
