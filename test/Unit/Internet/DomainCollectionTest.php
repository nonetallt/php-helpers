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
}
