# ArrayValidator

ArrayValidator is a validator class that can be used for validating complex
multilevel arrays 

## Features

### Validation
* Validate a base value using multiple validation rules
* Validate nested properties using nested schemas
* Validate each item in an array using multiple validation rules
* Validate each item in an array using a nested schema

### Clear error handling
* Validation result has a collection of all validation exceptions
* Exceptions contain array paths and values validated

### Matching schema structure
* Optionally require that given array keys must exist
* Strict mode validation to force exact match of the schema, where errors are
  created for all unexpected elements and all values are required


## Schema

### validate (string)
String of validation rules used to validate item

### validate_items (string)
String of validation rules used to validate each item within this array 

**Using this option automatically assumes that item should be a valid array**

### validate_items (array)
Nested schema that will be used to validate each item within this array

**Using this option automatically assumes that item should be a valid array**

### properties (array)
Array where each key is the name of the property and the value is a nested schema used to validate the item


## Examples

### Validating a base value
```php
$schema = [
    'validate' => 'boolean'
];

$validator = ArrayValidator::fromArray($schema);

// key is used as a base path for error messages, it is set as 'schema' by default
$key = 'test';

$result = $validator->validate('string', $key);

/*
    Messages:
   [
        'Value test must be a string'
   ]
*/
```

### Using validation results

See [ValidationResult documentation]('./ValidationResult.md')


### Validating array items using nested schemas

```php
$schema = [
    'validate_items' => [
        'validate' => 'array',
        'properties' => [
            'product_name' => [
                'validate' => 'required|string'
            ],
            'amount_sold' => [
                'validate' => 'required|integer|min:1'
            ]
        ]
    ]
];

$validator = ArrayValidator::fromArray($schema);

$result = $validator->validate([
        [
            'product_name' => 'orange',
            'amount_sold' => 3
        ],
        [
            'product_name' => true,
            'amount_sold' => 1
        ],
        [
            'product_name' => 'banana',
            'amount_sold' => 'foo'
        ],
        [
            'product_name' => 'kiwi'
        ]
]);

/*
    Messages:
   [
        'Value transactions->1->product_name must be a string',
        'Value transactions->2->amount_sold must be an integer',
        'Value transactions->3->amount_sold is required',
   ]
*/

```


Note that strict mode should not be used when validating items with arbitary amount of keys
