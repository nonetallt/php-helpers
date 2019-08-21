<?php

namespace Nonetallt\Helpers\Filesystem;

class FileIterator implements \Iterator
{
    private $iteratorPosition;
    private $file;
    private $stream;

    public function __construct(File $file)
    {
        $this->iteratorPosition = 0;
        $this->file = $file;
    }

    public function __destruct()
    {
        /* Close stream if open */
        if(is_resource($this->stream)) fclose($this->stream); 
    }

    /**
     * Read lines using user supplied callback function.
     *
     * @param callable $cb Read callback function. Return false to break the
     * read loop.
     *
     * @param bool $readBlank If set to true, lines with only whitespace will
     * also be red.
     *
     */
    public function read(callable $cb, bool $readBlank = false)
    {
        $lineNumber = 1;

        foreach($this as $line) {
            /* Skip empty lines when readBlank argument is not used */
            if(! $readBlank && trim($line) === '') continue;

            /* Return line content and index to callback */
            if($cb($line, $lineNumber) === false) break;

            $lineNumber++;
        }
    }

    public function count(bool $countEmpty = false) : int
    {
        $lineCount = 0;

        foreach($this as $line) {
            /* Skip empty lines when countEmpty argument is not used */
            if(! $countEmpty && trim($line) === '') continue;
            $lineCount++;
        }

        /* Remove one from line count to account for feof operation */
        if($countEmpty) $lineCount--;

        return $lineCount;
    }

    private function getStream()
    {
        if($this->stream === null) {
            $this->stream = $this->file->openStream('r');
        }

        return $this->stream;
    }

    public function current()
    {
        return fgets($this->getStream());
    }

    public function key()
    {
        return $this->iteratorPosition;
    }

    public function next()
    {
        ++$this->iteratorPosition;
    }

    public function rewind()
    {
        $this->iteratorPosition = 0;
        $this->stream = null;
    }

    public function valid() : bool
    {
        /* Feof check reads always one empty line at the end of the file */
        return ftell($this->getStream()) < $this->file->getSize();
    }
}
