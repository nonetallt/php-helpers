# ValidationRuleRepository

A ReflectionRepository class containing available ValidationRule(s).

## Usage

Note that the class constructor will already load the validation rules provided
by this package.

```php
$repo = new ValidationRuleRepository();

// Use to load more validation rules from the target directory and namespace
$repo->loadReflections($directory, $namespace);
```

## Usage in application container context

When using a framework that uses application containers, this class should be
only created once to avoid unneccesarily loading validation rules multiple times.
