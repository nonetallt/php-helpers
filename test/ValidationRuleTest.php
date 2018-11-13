<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;

abstract class ValidationRuleTest extends TestCase
{
    protected abstract function ruleClass();

    protected abstract function ruleName();

    protected abstract function parameters();

    protected abstract function expectations();

    public function setUp()
    {
        parent::setUp();
        $class = $this->ruleClass();
        $this->rule = new $class($this->ruleName(), $this->parameters());
    }

    public function testValidatorCanBeCreated()
    {
        $this->assertInstanceOf($this->ruleClass(), $this->rule);
    }

    public function testValidationShouldFailForAGivenValues()
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

    public function testValidationShouldSucceedForAGivenValues()
    {
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
