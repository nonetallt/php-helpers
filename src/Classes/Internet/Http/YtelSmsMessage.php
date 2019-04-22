<?php

namespace App\Domain\Api;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use App\Domain\Messages\GenericMessage;

class YtelSmsMessage
{
    use ConstructedFromArray;

    private $from;
    private $to;
    private $body;
    private $originalMessage;

    public function __construct(GenericMessage $originalMessage, string $from, string $to, string $body)
    {
        $this->originalMessage = $originalMessage;
        $this->from            = $from;
        $this->to              = $to;
        $this->body            = $body;
    }

    public function getOriginalMessage()
    {
        return $this->originalMessage;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getBody()
    {
        return $this->body;
    }

    public static function arrayValidationRules()
    {
        return [
            'from' => 'string',
            'to'   => 'string',
            'body' => 'string',
        ];
    }

    public function toArray()
    {
        return [
            'From' => $this->from,
            'To'   => $this->to,
            'Body' => $this->body,
        ];
    }
}
