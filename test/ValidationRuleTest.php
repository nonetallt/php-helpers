<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;
use Nonetallt\Helpers\Validation\ValidationRuleFactory;

abstract class ValidationRuleTest extends TestCase
{
    private $rule;
    private $class;

    protected abstract function ruleName();

    protected abstract function parameters();

    protected abstract function expectations();

    public function setUp()
    {
        parent::setUp();
        $factory = new ValidationRuleFactory();
        $mapping = $factory->validationRuleMapping();
        $name = $this->ruleName();
        $this->class = $mapping[$name];

        $this->rule = new $this->class($this->parameters());
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
            $this->assertTrue($this->rule->validate($value, $key)->failed());
        }
    }

    public function testValidationShouldSucceedForGivenValues()
    {
        /* echo $this->ruleName() . PHP_EOL; */
        $expectations = $this->expectations();
        $pass = $expectations['pass'] ?? [];

        $id = "ValidationRuleTest";
        if(! is_array($pass)) throw new \Exception("$id succssful expectations must be of type array");
        if(empty($pass)) throw new \Exception("$id must set some succesful expectations");

        foreach($pass as $key => $value) {
            $this->assertTrue($this->rule->validate($value, $key)->passed());
        }
    }
}
