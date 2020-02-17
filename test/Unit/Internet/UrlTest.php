<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Url;

class UrlTest extends TestCase
{
    public function testQueryCanBeConstructedFromStringAndRevertedToOriginalString()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals($string, (string)$url);
    }

    public function testGetSchemeWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals('http', $url->getScheme());
    }

    public function testGetUserWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals('username', $url->getUser());
    }

    public function testGetPasswordWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals('password', $url->getPassword());
    }

    public function testGetHostWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals('hostname', $url->getHost());
    }

    public function testGetPortWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals(9090, $url->getPort());
    }

    public function testGetPathWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals('/path', $url->getPath());
    }

    public function testGetQueryWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals(['arg' => 'value'], $url->getQuery()->toArray());
    }

    public function testGetQueryStringWorks()
    {
        $string = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $url = Url::fromString($string);
        $this->assertEquals('arg=value', $url->getQueryString());
    }

    public function testToStringWorksCorrectlyWithShortUrl()
    {
        $string = 'foo.bar';
        $url = Url::fromString($string);
        $this->assertEquals('http://foo.bar', (string)$url);
    }

    public function testStringWillBeUsedAsHost()
    {
        $string = 'foo.bar';
        $url = Url::fromString($string);
        $this->assertEquals($string, $url->getHost());
    }

    public function testPathIsNotSetWhenStringIsUsedAsHost()
    {
        $string = 'foo.bar';
        $url = Url::fromString($string);
        $this->assertFalse($url->getSetting('path')->hasValue());
    }
}
