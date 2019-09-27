<?php

namespace Test\Mock;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;

class MockException extends \Exception
{
    use ConstructedFromArray;

    public function toArray() : array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'previous' => $this->getPrevious()
        ];
    }
}
