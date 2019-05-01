<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Generic\Container;

class ContainerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCanBeInitialized()
    {
        $this->assertInstanceOf(Container::class, new Container());
    }

    public function testToArrayPreservesValues()
    {
        $expected = [1,2,3];
        $container = new Container($expected);
        $this->assertEquals($expected, $container->toArray());
    }

    public function testDefaultsAreSetWhenConstructed()
    {
        $defaults = ['value1' => 1, 'value2' => 2];
        $container = new Container([], $defaults);
        $this->assertEquals($defaults, $container->toArray());
    }

    public function testDefaultsAreOverriddenByUserSuppliedValues()
    {
        $options  = ['value1' => 2, 'value2' => 1];
        $defaults = ['value1' => 1, 'value2' => 2];

        $container = new Container($options, $defaults);
        $this->assertEquals($options, $container->toArray());
    }

    public function testValuesAreValidatedIfValidatorsAreSet()
    {
        $options = ['value1' => 1, 'value2' => 2];
        $validators = [
            'value1' => 'integer',
            'value2' => 'string'
        ];

        /* Assert that validation fails on value2 */
        $this->expectExceptionMessage('Value validation failed');
        $container = new Container($options, [], $validators);
    }

    public function testWhitelistedValueIsAccepted()
    {
        $options = ['value1' => 1, 'value2' => 2];
        $container = new Container($options, [], [], ['value1', 'value2']);
        $this->assertEquals($options, $container->toArray());
    }
    
    public function testNonWhitelistedValueIsRejected()
    {
        $options = ['value1' => 1, 'value2' => 2];

        /* Assert that validation fails on value2 */
        $this->expectExceptionMessage("Invalid option 'value2': only whitelisted values are allowed");
        $container = new Container($options, [], [], ['value1']);
    }

    public function testClassPropertiesCanBeUsedAsKeys()
    {
        $options = ['options' => 1];
        $container = new Container($options, [], [], ['options']);
        $this->assertEquals($options, $container->toArray());
    }

    public function testClassPropertiesAreValidated()
    {
        $options = ['options' => 1];
        $this->expectExceptionMessage('Value validation failed');
        $container = new Container($options, [], ['options' => 'string']);
    }

    public function testClassPropertiesCanPassValidation()
    {
        $options = ['options' => 1];
        $container = new Container($options, [], ['options' => 'integer']);
        $this->assertEquals($options, $container->toArray());
    }

    public function testClassPropertiesCanBeAccessedUsingTheirOriginalName()
    {
        $options = ['options' => 1];
        $container = new Container($options, [], ['options' => 'integer']);
        $this->assertEquals(1, $container->options);
    }

    public function testWhitelistingAcceptsCorrectValueWithDigitWildcard()
    {
        $options = ['value1' => 1, 'value2' => 2];
        $container = new Container($options, [], [], ['value%']);
        $this->assertEquals($options, $container->toArray());
    }

    public function testWhitelistingRejectIncorrectValueWithDigitWildcard()
    {
        $options = ['value1' => 1, 'value2' => 2];
        $this->expectExceptionMessage("Invalid option 'value1': only whitelisted values are allowed");
        $container = new Container($options, [], [], ['%value1']);
    }

    public function testWhitelistingAcceptsCorrectValueWithGeneralWildcard()
    {
        $options = ['value foo' => 1, 'value bar' => 2];
        $container = new Container($options, [], [], ['value*']);
        $this->assertEquals($options, $container->toArray());
    }

    public function testWhitelistingRejectIncorrectValueWithGeneralWildcard()
    {
        $options = ['value1' => 1, 'value2' => 2];
        $this->expectExceptionMessage("Invalid option 'value1': only whitelisted values are allowed");
        $container = new Container($options, [], [], ['val*ue1']);
    }
}
