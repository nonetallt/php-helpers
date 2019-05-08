<?php

namespace Nonetallt\Helpers\Internet\Http\Redirections;

use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatus;
use Nonetallt\Helpers\Internet\Http\Url;
use Nonetallt\Helpers\Describe\DescribeObject;

class HttpRedirection
{
    private $from;
    private $to;
    private $status;

    public function __construct($from, $to, HttpStatus $status)
    {
        $this->setFrom($from);
        $this->setTo($to);
        $this->setStatus($status);
    }

    public function setFrom($from)
    {
        $class = Url::class;

        if(is_string($from)) $from = Url::fromString($from);
        if(is_a($from, $class)) $this->from = $from;
        else {
            $given = (new DescribeObject($from))->describeType();
            $msg = "From must be either a string or $class object, $given given";
            throw new \InvalidArgumentException($msg);
        }
    }

    public function setTo($to)
    {
        $class = Url::class;

        if(is_string($to)) $to = Url::fromString($to);
        if(is_a($to, $class)) $this->to = $to;
        else {
            $given = (new DescribeObject($to))->describeType();
            $msg = "To must be either a string or $class object, $given given";
            throw new \InvalidArgumentException($msg);
        }
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
