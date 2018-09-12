<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Terminal\ExitCode;

class ExitCodeTest extends TestCase
{
    public function testCodeZeroIsSuccess()
    {
        $this->assertTrue($this->code(0)->isSuccess());
    }

    public function testCodeNotZeroIsFailure()
    {
        $this->assertFalse($this->code(1)->isSuccess());
    }

    public function testGetMessageReturnsCorrectGeneralError()
    {
        $this->assertEquals('general error', $this->code(1)->getMessage());
    }

    public function testMessageIsOutOfRangeWhenCodeIsLessThanZero()
    {
        $this->assertContains('out of range', $this->code(-1)->getMessage());
    }

    public function testMessageIsOutOfRangeWhenCodeIsGreaterThan255()
    {
        $this->assertContains('out of range', $this->code(256)->getMessage());
    }

    public function testCodeGreaterThan128ReturnsCorrectFatalError()
    {
        $this->assertEquals('fatal error (Emulation Trap)', $this->code(135)->getMessage());
    }

    public function testCode165ReturnsUnknownFatalError()
    {
        $this->assertEquals('fatal error (Unknown)', $this->code(165)->getMessage());
    }

    private function code(int $s)
    {
        return new ExitCode($s);
    }
}
