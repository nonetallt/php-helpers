<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestExceptionCollection;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestConnectionException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestClientException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestServerException;
use Nonetallt\Helpers\Internet\Http\Exceptions\HttpRequestResponseException;

class HttpRequestExceptionCollectionTest extends TestCase
{
    private $collection;

    public function setUp()
    {
        parent::setUp();
        $this->collection = new HttpRequestExceptionCollection();
    }

    public function testHasConnectionErrorsReturnsFalseWhenThereAreNoExceptionsInCollection()
    {
        $this->assertFalse($this->collection->hasConnectionErrors());
    }

    public function testHasClientErrorsReturnsFalseWhenThereAreNoExceptionsInCollection()
    {
        $this->assertFalse($this->collection->hasClientErrors());
    }

    public function testHasServerErrorsReturnsFalseWhenThereAreNoExceptionsInCollection()
    {
        $this->assertFalse($this->collection->hasServerErrors());
    }

    public function testHasResponseErrorsReturnsFalseWhenThereAreNoExceptionsInCollection()
    {
        $this->assertFalse($this->collection->hasResponseErrors());
    }

    public function testHasConnectionErrorsReturnsFalseWhenThereAreOnlyOtherExceptionsInCollection()
    {
        $this->collection->push(new HttpRequestException('test'));
        $this->assertFalse($this->collection->hasConnectionErrors());
    }

    public function testHasClientErrorsReturnsFalseWhenThereAreOnlyOtherExceptionsInCollection()
    {
        $this->collection->push(new HttpRequestException('test'));
        $this->assertFalse($this->collection->hasClientErrors());
    }

    public function testHasServerErrorsReturnsFalseWhenThereAreOnlyOtherExceptionsInCollection()
    {
        $this->collection->push(new HttpRequestException('test'));
        $this->assertFalse($this->collection->hasServerErrors());
    }

    public function testHasResponseErrorsReturnsFalseWhenThereAreOnlyOtherExceptionsInCollection()
    {
        $this->collection->push(new HttpRequestException('test'));
        $this->assertFalse($this->collection->hasResponseErrors());
    }

    public function testHasConnectionErrorsReturnsTrueWhenCorrectExceptionExists()
    {
        $this->collection->push(new HttpRequestConnectionException('test'));
        $this->assertTrue($this->collection->hasConnectionErrors());
    }

    public function testHasClientErrorsReturnsTrueWhenCorrectExceptionExists()
    {
        $this->collection->push(new HttpRequestClientException('test'));
        $this->assertTrue($this->collection->hasClientErrors());
    }

    public function testHasServerErrorsReturnsTrueWhenCorrectExceptionExists()
    {
        $this->collection->push(new HttpRequestServerException('test'));
        $this->assertTrue($this->collection->hasServerErrors());
    }

    public function testHasResponseErrorsReturnsTrueWhenCorrectExceptionExists()
    {
        $this->collection->push(new HttpRequestResponseException('test'));
        $this->assertTrue($this->collection->hasResponseErrors());
    }
}
