<?php

namespace Test\Unit\Generic\Exceptions\Factory;

use PHPunit\Framework\TestCase;
use Nonetallt\Helpers\Generic\Exceptions\Factory\ExceptionFactory;
use Nonetallt\Helpers\Generic\Exceptions\NotFoundException;

class ExceptionFactoryTest extends TestCase
{
    public function testNonExistentKeyThrowsExceptions()
    {
        $factory = new ExceptionFactory();
        $this->expectException(NotFoundException::class);
        $factory->createExceptions(null);
    }
}
