<?php

namespace Test\Unit\Validation;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validators\ArrayValidator;

class ArrayValidatorTest extends TestCase
{
    public function testValidateCanValidateSelf()
    {
        $schema = [
            'validate' => 'string'
        ];

        $validator = new ArrayValidator($schema, 'schema');
        $result = $validator->validate(true);

        $expected = [
            'Value schema must be a string'
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testValidateCanValidateNestedAttribute()
    {
        $schema = [
            'properties' => [
                'foo' => [
                    'validate' => 'array',
                    'properties' => [
                        'bar' => [
                            'validate' => 'boolean'
                        ]
                    ]
                ]
            ]
        ];

        $validator = new ArrayValidator($schema);
        $result = $validator->validate([
            'foo' => [
                'bar' => 'string'
            ]
        ]);

        $expected = [
            'Value foo->bar must be boolean'
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testValidateItemsReturnsExceptionsForFailedFields()
    {
        /* Make sure each item in array is a string */
        $schema = [
            'validate_items' => 'string'
        ];

        $validator = new ArrayValidator($schema);
        $result = $validator->validate([ 'foo', 'bar', 'baz', 1 ]);

        $expected = [
            'Value 3 must be a string'
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testValidateItemsCanPassValidation()
    {
        /* Make sure each item in array is a string */
        $schema = [
            'validate_items' => 'string'
        ];

        $validator = new ArrayValidator($schema);
        $result = $validator->validate([ 'foo', 'bar', 'baz']);

        $this->assertEquals(0, $result->getExceptions()->count());
    }

    public function testItemsAreNotValidatedIfPropValidationFailed()
    {
        $schema = [
            'properties' => [
                'active' => [
                    'validate' => 'boolean'
                ],
                'whitelisted' => [
                    'validate' => 'string',
                    'validate_items' => 'boolean'
                ]
            ]
        ];

        $validator = new ArrayValidator($schema);
        
        $result = $validator->validate([
            'active' => true,
            'whitelisted' => ['192.168.0.254'],
        ]);

        $expected = [
            'Value whitelisted must be a string',
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testArrayValidationIsDoneAutomaticallyIfValidateItemsIsUsed()
    {
        $schema = [
            'validate_items' => 'string'
        ];

        $validator = new ArrayValidator($schema, 'schema');
        $result = $validator->validate(true);

        $expected = [
            'Value schema must be an array',
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    /* TODO check required values */
    /* TODO strict validation mode, errors for unexpected fields */
    /* TODO extend validation rules if neccesary */
    /* TODO refactor constructor + fromArray() */
}
