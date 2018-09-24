<?php

namespace Nonetallt\Helpers\Terminal;

class Command
{
    private $command;
    private $output;
    private $status;
    private $expects;
    private $appends;

    public function __construct(string $command, bool $appends = true)
    {
        $this->command = $command;
        $this->output = [];
        $this->status = -1;
        $this->expects = null;
        $this->appends = $appends;
    }

    public function expects(string $expected)
    {
        $this->expects = $expected;
        return $this;
    }

    public function execute()
    {
        $this->output = [];
        $this->status = -1;

        $outputRedirect = "2>&1";

        /* Make sure that output contains possible errors */
        if($this->appends && strpos($this->command, $outputRedirect) === false) {
            $this->command .= " $outputRedirect";
        }

        exec($this->command, $this->output, $this->status);

        /* Return nothing if nothing is expected */
        if(is_null($this->expects)) return;

        return strpos($this->getOutputString(), $this->expects) !== false;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getOutputString()
    {
        return implode(PHP_EOL, $this->output);
    }

    public function getStatus()
    {
        return new StatusCode($this->status);
    }
}
