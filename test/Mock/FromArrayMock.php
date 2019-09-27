<?php

namespace Test\Mock;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use Test\Mock\MockException;

class FromArrayMock
{
    use ConstructedFromArray;

    private $args;

    public function __construct(int $arg1, MockException $arg2, string $arg3 = 'foo')
    {
        $this->args = [
            'arg1' => $arg1,
            'arg2' => $arg2,
            'arg3' => $arg3,
        ];
    }

    public function getArg(int $num)
    {
        $key = "arg$num";

        if(! array_key_exists($key, $this->args)) {
            $msg = "Arg '$key' was not found";
            throw new NotFoundException($msg);
        }

        return $this->args[$key];
    }
}
