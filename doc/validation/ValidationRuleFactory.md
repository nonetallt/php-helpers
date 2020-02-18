# ValidationRuleFactory

Class used to construct validation rules out of strings.

## Usage
```php
$factory = new ValidationRuleFactory($repo, $settings);

// ValidationRule 
$factory->makeRule(string $name, array $parameters);

// ValidationRule from string
$factory->makeRuleFromString($string);

// ValidationRuleCollection from string
$factory->makeRulesFromString($string);
```


## Usage in application container context

When using a framework that uses application containers, it is recommended to use a singleton for this class.

Also see [ValidationRuleRepository]('./ValidationRuleRepository.md').

## Settings

ValidationRuleParsingSettings class is used for parsing the rule strings, it
has the following settings available:

#### rule_delimiter (`default: '|'`)

Delimiter used to separate rules from each other.

#### rule_parameter_delimiter (`default: :`)

Delimiter used to separate rules from parameters of the rule.

#### parameter_delimiter (`default: ','`)

Delimiter used to separate rule parameters from each other.
 
#### reverse_notation (`default: '!'`)

Used to reverse validation result when used as a leading character for the rule string.
