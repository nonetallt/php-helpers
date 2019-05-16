<?php

namespace Test\Unit\Validation;

use Test\ValidationRuleTest;
use Nonetallt\Helpers\Validation\Rules\ValidationRuleSubclassOf;

class ValidationRuleSubclassOfTest extends ValidationRuleTest
{
    protected function ruleName()
    {
        return 'subclass_of';
    }

    protected function parameters()
    {
        return [\Exception::class];
    }

    protected function expectations()
    {
        return [
            'pass' => [new \InvalidArgumentException('test'), new \LogicException('test')],
            'fail' => [
                1, 
                null, 
                -1, 
                [], 
                /* Expect failure on classname string */ 
                \InvalidArgumentException::class
            ]
        ];
    }

    public function testValueIsAcceptedWhenAcceptStringParameterIsSetTrue()
    {
        $rule = new ValidationRuleSubclassOf([
            'parent_class' => ValidationRuleTest::class,
            'accepts_string' => true
        ]);

        $this->assertTrue($rule->validate(get_class($this), 'test')->passed());
    }

    public function testValueIsDeclinedWhenAcceptStringParameterIsSetFalse()
    {
        $rule = new ValidationRuleSubclassOf([
            'parent_class' => ValidationRuleTest::class,
            'accepts_string' => false
        ]);

        $this->assertFalse($rule->validate(get_class($this), 'test')->passed());
    }

    public function testAcceptStringParameterIsFalseByDefault()
    {
        $rule = new ValidationRuleSubclassOf([
            'parent_class' => ValidationRuleTest::class,
        ]);

        $this->assertFalse($rule->validate(get_class($this), 'test')->passed());
    }
}
