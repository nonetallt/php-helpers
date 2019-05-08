<?php

namespace Nonetallt\Helpers\Internet\Http\Redirections;

use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatus;

class HttpRedirection
{
    private $from;
    private $to;
    private $status;

    public function __construct(string $from, string $to, HttpStatus $status)
    {
        $this->from = $from;
        $this->to = $to;
        $this->setStatus($status);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setStatus(HttpStatus $status)
    {
        $code = $status->getCode();

        if($code < 300 || $code > 399) {
            $msg = "Given status code $code is not a valid redirection code";
            throw new \InvalidArgumentException($msg);
        }

        $this->status = $status;
    }

    public function getFrom() : string
    {
        return $this->from;
    }

    public function getTo() : string
    {
        return $this->to;
    }

    /**
     * Proxy for getTo()
     */
    public function getDestination() : string
    {
        return $this->getTo();
    }

    public function getStatus() : HttpStatus
    {
        return $this->status;
    }

    public function getStatusCode() : int
    {
        return $this->status->getCode();
    }

    public function toArray() : array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status->toArray()
        ];
    }
}
