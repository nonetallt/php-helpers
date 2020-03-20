<?php declare(strict_types=1);

namespace Test\Unit\Validation;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\ValidationRuleFactory;
use Nonetallt\Helpers\Validation\Exceptions\RuleNotFoundException;

class ValidationRuleFactoryTest extends TestCase
{
    public function testRuleNotFoundExceptionIsThrown()
    {
        $this->expectException(RuleNotFoundException::class);

        $factory = new ValidationRuleFactory();
        $factory->makeRule('foobarbaz');
    }
}
