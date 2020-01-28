<?php declare(strict_types=1);

namespace Test\Unit\Validation;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Validation\Validators\ArrayValidator;

class ArrayValidatorTest extends TestCase
{
    public function testValidateCanValidateSelf()
    {
        $schema = [
            'path' => 'schema',
            'validate' => 'string'
        ];

        $validator = ArrayValidator::fromArray($schema);
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

        $validator = ArrayValidator::fromArray($schema);
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

        $validator = ArrayValidator::fromArray($schema);
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

        $validator = ArrayValidator::fromArray($schema);
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

        $validator = ArrayValidator::fromArray($schema);
        
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
            'path' => 'schema',
            'validate_items' => 'string'
        ];

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate(true);

        $expected = [
            'Value schema must be an array',
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testErrorIsCreatedWhenRequiredValueIsMissing()
    {
        $schema = [
            'properties' => [
                'foo' => [
                    'required' => true
                ]
            ]
        ];

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate([]);
        $expected = [
            'Value foo is required'
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testStrictModeCreatesErrorsForMissingAndExtraFields()
    {
        $schema = [
            'path' => 'schema',
            'properties' => [
                'foo' => [
                    'validate' => 'boolean'
                ],
                'bar' => [
                    'validate' => 'boolean'
                ]
            ]
        ];

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate([
            'foo' => true,
            'baz' => false
        ], true);

        $expected = [
            'Value schema->bar is required',
            'Value schema->baz not expected'
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }
}
