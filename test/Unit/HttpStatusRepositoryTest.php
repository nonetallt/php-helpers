<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\HttpStatusRepository;

class HttpStatusRepositoryTest extends TestCase
{
    private $repo;

    public function setUp()
    {
        $this->repo = new HttpStatusRepository();
    }

    public function testGetByCodeFindsCode200()
    {
        $expected = [
            'code' => 200,
            'name' => 'ok',
            'description' => 'The request has succeeded',
            'standard' => ''
        ];
        $this->assertEquals($expected, $this->repo->getByCode(200)->toArray());
    }

    public function testCodeExistsFinds200()
    {
        $this->assertTrue($this->repo->codeExists(200));
    }

    public function testCodeExistsReturnsFalseFor123()
    {
        $this->assertFalse($this->repo->codeExists(123));
    }
}
