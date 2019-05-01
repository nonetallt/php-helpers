<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Internet\Dns\Domain;
use Nonetallt\Helpers\Internet\Dns\DomainCollection;

class DomainCollectionTest extends TestCase
{
    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(DomainCollection::class, new DomainCollection());
    }

    public function testToArrayWorks()
    {
        $domains = new DomainCollection();
        $domains->push(new Domain('foo.com'));
        $domains->push(new Domain('bar.com'));

        $expected = [
            [
                'name' => 'foo.com',
                'sld' => 'foo',
                'tld' => 'com',
                'is_subdomain' => false
            ],
            [
                'name' => 'bar.com',
                'sld' => 'bar',
                'tld' => 'com',
                'is_subdomain' => false
            ]
        ];

        $this->assertEquals($expected, $domains->toArray());
    }
}
