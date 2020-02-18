# ValidationResult

ValidationResult is the class returned by most validators.

## Usage

```php
// Get validation exceptions
$messages = $result->getExceptions();

// Check if validation passed
$result->passed();

// Check if validation failed
$result->failed();
```

## Exceptions

getExceptions() returns a ValidationExceptionCollection which contains ValidationException(s)

Exception collection:
```php
// Get an array containing all exception messages
$exceptions->getMessages();
```

Exceptions:
```php
// Value that was validated
$exception->getValue();

// Name of the validated value
$exception->getValueName();
```
