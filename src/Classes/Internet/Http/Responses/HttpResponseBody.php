<?php

namespace Nonetallt\Helpers\Internet\Http\Responses;

use Psr\Http\Message\StreamInterface;
use Nonetallt\Helpers\Internet\Http\Responses\Processors\ResponseParser;
use Nonetallt\Helpers\Generic\Traits\LazyLoadsProperties;
use Nonetallt\Helpers\Generic\Exceptions\ParsingException;

class HttpResponseBody
{
    use LazyLoadsProperties;

    private $raw;
    private $parser;

    public function __construct(StreamInterface $raw, ResponseParser $parser = null)
    {
        $this->raw = $raw;
        $this->parser = $parser;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    public function getRaw() : StreamInterface
    {
        return $this->raw;
    }

    public function lazyLoadContent()
    {
        return (string)$this->raw;
    }

    /**
     * @throws ParsingException
     *
     */
    public function lazyLoadParsed()
    {
        $content = $this->getContent();

        if($this->parser !== null) {
            $content = $this->parser->parseResponseBody($content);
        }

        return $content;
    }

    public function canBeParsed() : bool
    {
        try {
            $this->getParsed();
            return true;
        }
        catch(ParsingException $e) {
            return false;
        }
    }
}
