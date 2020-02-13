<?php

namespace Test\Unit\Validation\Validators;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validators\Validator;
use Nonetallt\Helpers\Validation\ValidationRuleFactory;

class ValidatorTest extends TestCase
{
    public function testValidatorPassesMultipleRules()
    {
        $data = [
            'test' => 'Kappa'
        ];

        $rules = [
            'test' => 'required|string|min:1|max:5'
        ];

        $validator = new Validator($rules);
        $this->assertTrue($validator->validate($data)->passed());
    }

    public static function isFive($value, string $name, callable $cb)
    {
        return $cb($value === 5, "Value $name should be exactly 5");
    }

    public function testValidatorCanUseCustomValidationMethodsFromReferencedClass()
    {
        $data = [
            'test' => 5
        ];

        $class = self::class;

        $rules = [
            'test' => "required|custom_method:$class,isFive"
        ];

        $validator = new Validator($rules);
        $this->assertTrue($validator->validate($data)->passed());
    }
}
