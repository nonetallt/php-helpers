# ArrayValidator

ArrayValidator is a validator class that can be used for validating complex
multilevel arrays using the available validation rules. 

## Common use cases

* Validating complex json object schema after decoding to array
* Constructing objects from multilevel arrays with a forced schema

## Features

* Validation result has a collection of all validation exceptions
* Exceptions contain array paths
* Optionally require that given array keys must exist
* Validate a base value using a list of validation rules
* Validate a nested property using a list of validation rules
* Validate each item in an array using a list of validation rules
* Nested validation can be done recursively on deep arrays
* Strict mode validation to force exact match of the schema

## Options

* required (bool), wether this key must exist in the validation data
* validate (string), validation rules string to validate base value
* validate_items (string), validation rules string to validate each value in an array
* properties (array), array of nested property validators
* path (string), base path for level 1 validation, for example "schema", used only for display purposes

## Notes
* When setting validate_items with no validate option, the validate is automatically set as an array validator

## Examples

```php
$schema = [
    'path' => 'schema',
    'properties' => [
        'foo' => [
            'validate' => 'string'
        ],
        'bar' => [
            'validate' => array,
            'properties' => [
                'baz' => [
                    'validate_items' => 'string'
                ]
            ]
        ]
    ]
];

$strict = false;
$validator = ArrayValidator::fromArray($schema, $strict);

$result = $validator->validate([
    'foo' => 'string_value',
    'bar' => [
        'baz' => [
            'value1' => 'string_value_1',
            'value2' => 'string_value_2',
            'value3' => 3
        ]
    ]
]);

$messages = $result->getExceptions()->getMessages();

/*
    Messages:
   [
        'Value schema->bar->baz->value3 must be a string'
   ]
*/

$result->passed(); // false
$result->failed(); // true

```
