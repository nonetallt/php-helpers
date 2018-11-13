<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validator;

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
        $this->assertTrue($validator->passes($data));
    }
}
