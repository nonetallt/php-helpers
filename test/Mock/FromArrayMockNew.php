<?php

namespace Test\Mock;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArrayNew;

class FromArrayMockNew
{
    use ConstructedFromArrayNew;

    private $args;

    public function __construct(int $arg1, \Exception $arg2, string $arg3 = 'foo')
    {
        $this->args = [
            'arg1' => $arg1,
            'arg2' => $arg2,
            'arg3' => $arg3,
        ];
    }
}
