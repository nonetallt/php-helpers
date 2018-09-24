<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Terminal\Command;

class CommandTest extends TestCase
{
    public function testExecuteOutputsCommandOutput()
    {
        $command = new Command('echo test');
        $command->execute();

        $this->assertEquals('test', $command->getOutputString());
    }

    public function testExecuteReturnsTrueWhenExpectsIsSetAndContainsString()
    {
        $command = new Command('echo test');
        $result = $command->expects('test')->execute();

        $this->assertTrue($result);
    }

    public function testNonExistentCommandIsNotFound()
    {
        $command = new Command('afsafasdfafs');
        $result = $command->expects('not found')->execute();

        $this->assertTrue($result);
    }

    public function testNonExistentCommandDoesNotOutputTest()
    {
        $command = new Command('afsafasdfafs');
        $result = $command->expects('test')->execute();

        $this->assertFalse($result);
    }

    public function testEchoReturnsSuccessfulStatusCode()
    {
        $command = new Command('echo test');
        $command->execute();

        $this->assertTrue($command->getExitCode()->isSuccess());
    }
}
