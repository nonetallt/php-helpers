<?php

namespace Test\Unit\Validation;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleSubclassOf;
use Nonetallt\Helpers\Validation\ValidationRuleReflection;

class ValidationRuleReflectionTest extends TestCase
{

    public function testAliasIsConvertedToSnakeCase()
    {
        $reflection = new ValidationRuleReflection(new \ReflectionClass(ValidationRuleSubclassOf::class));
        $this->assertEquals('subclass_of', $reflection->getAlias());
    }
}
