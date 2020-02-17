<?php

namespace Test\Unit\Internet\Http;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;
use Nonetallt\Helpers\Internet\Http\QueryParameters;
use Nonetallt\Helpers\Internet\Http\Redirections\HttpRedirection;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;

class HttpRequestTest extends TestCase
{
    public function testExceptionIsThrownWhenTryingToSetInvalidMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $request = new HttpRequest('foo', 'bar');
    }

    public function testQueryParametersAreSetCorrectly()
    {
        $parameters = ['param1' => 'foo', 'param2' => 'bar'];
        $request = new HttpRequest('get', 'bar', $parameters);
        $this->assertEquals($parameters, $request->getQuery()->toArray());
    }

    public function testQueryParametersCanBeAdded()
    {
        $parameters = ['param1' => 'foo', 'param2' => 'bar'];
        $appended = ['param3' => 'baz'];
        $request = new HttpRequest('get', 'bar', $parameters);
        $request->getQuery()->add($appended);

        $this->assertEquals(array_merge($parameters, $appended), $request->getQuery()->toArray());
    }

    public function testGetUrlReturnsUrlWithoutParameters()
    {
        $url = 'https://google.com';
        $query = ['foo' => 1, 'bar' => 2];
        $request = new HttpRequest('get', $url, $query);
        $this->assertEquals($url, $request->getUrl());
    }

    public function testGetEffectiveUrlWorksLikeGetUrlWhenThereAreNoRedirections()
    {
        $url = 'https://google.com';
        $query = ['foo' => 1, 'bar' => 2];
        $request = new HttpRequest('get', $url, $query);
        $this->assertEquals($url, $request->getEffectiveUrl());
    }

    public function testGetEffectiveUrlReturnsLastUrlWhenThereAreRedirections()
    {
        $firstUrl = 'foo.com';
        $secondUrl = 'bar.com';
        $thirdUrl = 'https://google.com';

        $query = ['foo' => 1, 'bar' => 2];
        $request = new HttpRequest('get', $firstUrl, $query);
        $repo = new HttpStatusRepository();
        $request->getRedirections()->push(new HttpRedirection($firstUrl, $secondUrl, $repo->getByCode(302)));
        $request->getRedirections()->push(new HttpRedirection($secondUrl, $thirdUrl, $repo->getByCode(302)));

        $this->assertEquals($thirdUrl, $request->getEffectiveUrl());
    }
}
