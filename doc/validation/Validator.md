# Validator

Validator is a validator class that can be used for validating one dimensional
arrays in style similar to Laravel.

## Usage

```php

$rules = [
    'foo' => 'required|string|min:1',
    'bar' => 'integer|min:0|max:10'
];

$validator = new Validator($rules);
$result = $validator->validate([
    'foo' => 'test',
    'bar' => 11
]);
```


### Using validation results

See [ValidationResult documentation]('./ValidationResult.md')

## Usage in application container context

When using a framework that uses application containers, it is recommended to
use a singleton for the following classes:

* [ValidationRuleRepository]('./ValidationRuleRepository.md')
* [ValidationRuleFactory]('./ValidationRuleFactory.md')

When using validator, you can pass the factory as second argument to the validator

```php
$validator = new Validator($rules, resolve(ValidationRuleFactory::class));
```
