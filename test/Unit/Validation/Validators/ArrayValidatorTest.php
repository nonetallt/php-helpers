<?php declare(strict_types=1);

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

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate(true, 'schema');

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
        ], '');

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
        $result = $validator->validate([ 'foo', 'bar', 'baz', 1 ], '');

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
        $result = $validator->validate([ 'foo', 'bar', 'baz'], '');

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
        ], '');

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

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate(true, 'schema');

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
        $result = $validator->validate([], '');
        $expected = [
            'Value foo is required'
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testStrictModeCreatesErrorsForMissingAndExtraFields()
    {
        $schema = [
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
        ], 'schema', true);

        $expected = [
            'Value schema->bar is required',
            'Value schema->baz not expected'
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testPropertiesOfItemsCanBeValidated()
    {
        $schema = [
            'properties' => [
                'items' => [
                    'validate_items' => [
                        'properties' => [
                            'name' => [
                                'required' => true,
                                'validate' => 'string'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate([
            'items' => [
                [
                    'name' => 'foo'
                ],
                [
                    'name' => true
                ],
[
                    'name' => false
                ],
                [],
            ]
        ], 'schema');

        $expected = [
            'Value schema->items->1->name must be a string',
            'Value schema->items->2->name must be a string',
            'Value schema->items->3->name is required',
        ];


        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testItemsWithPropertiesShouldBeArraysByDefault()
    {
        $schema = [
            'properties' => [
                'blacklisted' => [
                    'validate_items' => [
                        'properties' => [
                            'name' => [
                                'required' => true,
                                'validate' => 'string'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate([
            'blacklisted' => [
                'foo',
                [],
                ['name' => true]
            ]
        ], 'filters');

        $expected = [
            'Value filters->blacklisted->0 must be an array',
            'Value filters->blacklisted->1->name is required',
            'Value filters->blacklisted->2->name must be a string',
        ];


        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }

    public function testStrictModeFailsItemValidation()
    {
        $schema = [
            'validate_items' => [
                'properties' => [
                    'name' => [
                        'validate' => 'string'
                    ]
                ]
            ]
        ];

        $validator = ArrayValidator::fromArray($schema);
        $result = $validator->validate([
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz'],
        ], 'schema', true);


        $expected = [
            'Value schema->0 not expected',
            'Value schema->1 not expected',
            'Value schema->2 not expected',
        ];

        $this->assertEquals($expected, $result->getExceptions()->getMessages());
    }
}
