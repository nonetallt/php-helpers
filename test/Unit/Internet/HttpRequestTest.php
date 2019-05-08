<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Requests\HttpRequest;

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
        $this->assertEquals($parameters, $request->getQuery());
    }

    public function testQueryParametersCanBeAdded()
    {
        $parameters = ['param1' => 'foo', 'param2' => 'bar'];
        $appended = ['param3' => 'baz'];
        $request = new HttpRequest('get', 'bar', $parameters);
        $request->addToQuery($appended);

        $this->assertEquals(array_merge($parameters, $appended), $request->getQuery());
    }
}
