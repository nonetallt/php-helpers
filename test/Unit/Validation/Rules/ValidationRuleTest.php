<?php

namespace Test\Unit\Validation\Rules;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;
use Nonetallt\Helpers\Validation\ValidationRuleFactory;
use Nonetallt\Helpers\Describe\DescribeObject;

abstract class ValidationRuleTest extends TestCase
{
    private $rule;
    private $class;

    protected abstract function ruleName();

    protected abstract function parameters();

    protected abstract function expectations();

    public function setUp() : void
    {
        parent::setUp();
        $factory = new ValidationRuleFactory();
        $this->rule = $factory->makeRule($this->ruleName(), $this->parameters());
        $this->class = get_class($this->rule);
    }

    public function testValidatorCanBeCreated()
    {
        $this->assertInstanceOf($this->class, $this->rule);
    }

    public function testValidationShouldFailForGivenValues()
    {
        $expectations = $this->expectations();
        $fail = $expectations['fail'] ?? [];

        $id = 'ValidationRuleTest';
        if(! is_array($fail)) throw new \Exception("$id failing expectations must be of type array");
        if(empty($fail)) throw new \Exception("$id must set some failing expectations");

        foreach($fail as $key => $value) {
            $result = $this->rule->validate($value, $key)->failed();
            $value = ( new DescribeObject($value) )->describeAsString();
            $message = "Failed asserting that {$this->rule->getName()} fails for value $value";
            $this->assertTrue($result, $message);
        }
    }

    public function testValidationShouldSucceedForGivenValues()
    {
        $expectations = $this->expectations();
        $pass = $expectations['pass'] ?? [];

        $id = "ValidationRuleTest";
        if(! is_array($pass)) throw new \Exception("$id succssful expectations must be of type array");
        if(empty($pass)) throw new \Exception("$id must set some succesful expectations");

        foreach($pass as $key => $value) {
            $result = $this->rule->validate($value, $key)->passed();
            $value = ( new DescribeObject($value) )->describeAsString();
            $message = "Failed asserting that {$this->rule->getName()} passes for value $value";
            $this->assertTrue($result, $message);

        }
    }
}
