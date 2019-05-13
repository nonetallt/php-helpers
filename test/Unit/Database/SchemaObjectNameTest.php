<?php

namespace Test\Unit\Database;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Database\Common\Exceptions\SchemaValidationException;
use Nonetallt\Helpers\Database\Mysql\SchemaObjectName;

class SchemaObjectNameTest extends TestCase
{
    public function testEmptyNameThrowsException()
    {
        $this->expectException(SchemaValidationException::class);
        SchemaObjectName::validate('');
    }

    public function testSchemaNameConsistingOfOnlyDigitsThrowsException()
    {
        $this->expectException(SchemaValidationException::class);
        SchemaObjectName::validate('123');
    }

    public function testNameLongerThan64CharactersThrowsException()
    {
        $this->expectException(SchemaValidationException::class);
        SchemaObjectName::validate(str_repeat('a', 65));
    }

    public function testNameWithNonWhitelistedCharactersThrowsException()
    {
        $this->expectException(SchemaValidationException::class);
        SchemaObjectName::validate('foo bar');
    }

    public function testNameWithOnlyWhitelistedCharactersWorks()
    {
        $this->assertTrue(SchemaObjectName::isValid('$_foo_bar_$'));
    }
}
