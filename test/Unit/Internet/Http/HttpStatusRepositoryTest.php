<?php

namespace Test\Unit\Internet\Http;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Http\Statuses\HttpStatusRepository;

class HttpStatusRepositoryTest extends TestCase
{
    private $repo;

    public function setUp() : void
    {
        $this->repo = new HttpStatusRepository();
    }

    public function testGetByCodeFindsCode200()
    {
        $expected = [
            'code' => 200,
            'name' => 'OK',
            'description' => 'The request has succeeded',
            'standard' => '',
            'shouldRetry' => false
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
