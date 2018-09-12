<?php

namespace Nonetallt\Helpers\Terminal;

class ExitCode
{
    private $code;

    /* Based on http://tldp.org/LDP/abs/html/exitcodes.html */
    CONST STATUS_MESSAGES = [
        0   => 'success',
        1   => 'general error',
        2   => 'syntax or permission error',
        126 => 'command is not executable or permission error',
        127 => 'command not found',
        128 => 'invalid exit argument',
        130 => 'terminated by interrupt (possibly ctrl+c)'
    ];

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function __toString()
    {
        return $this->getCode().' : '.$this->getMessage();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function isSuccess()
    {
        return $this->code === 0;
    }

    public function getMessage()
    {
        if(isset(self::STATUS_MESSAGES[$this->code])) {
            return self::STATUS_MESSAGES[$this->code];
        }

        if($this->code > 255 || $this->code < 0) return 'exit status out of range';
        if($this->code > 128 && $this->code < 166) return $this->fatalError();

        return 'uknown status code';
    }

    /**
     * Based on
     * https://people.cs.pitt.edu/~alanjawi/cs449/code/shell/UnixSignals.htm
     */
    private function fatalError()
    {
        $types = [
            1 => 'Hangup',
            2 => 'Interrupt',
            3 => 'Quit',
            4 => 'Illegal Instruction',
            5 => 'Trace/Breakpoint Trap',
            6 => 'Abort',
            7 => 'Emulation Trap',
            8 => 'Arithmetic Exception',
            9 => 'Killed',
            10 => 'Bus Error',
            11 => 'Segmentation Fault',
            12 => 'Bad System Call',
            13 => 'Broken Pipe',
            14 => 'Alarm Clock',
            15 => 'Terminated',
            16 => 'User Signal 1',
            17 => 'User Signal 2',
            18 => 'Child Status',
            19 => 'Power Fail/Restart',
            20 => 'Window Size Change',
            21 => 'Urgent Socket Condition',
            22 => 'Socket I/O Possible',
            23 => 'Stopped (signal)',
            24 => 'Stopped (user)',
            25 => 'Continued',
            26 => 'Stopped (tty input)',
            27 => 'Stopped (tty output)',
            28 => 'Virtual Timer Expired',
            29 => 'Profiling Timer Expired',
            30 => 'CPU time limit exceeded',
            31 => 'File size limit exceeded',
            32 => 'All LWPs blocked',
            33 => 'Virtual Interporcessor Interrupt for Threads Library',
            34 => 'Asynchronous I/O'
        ];

        $fatalCode = $this->code - 128;

        $type = 'Unknown';

        if(isset($types[$fatalCode])) {
            $type = $types[$fatalCode];
        }

        return "fatal error ($type)";
    }
}
