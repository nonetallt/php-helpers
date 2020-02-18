* ArrayValidator should be able to use user provided ValidationFactory
* ValidatorInterface validate($value, string $name, ValidationSettings $settings) : ValidationResult; implement for all validators
* Closure based validation for ValidationSettings
* Make sure that HttpRequestSetting response processors can be added by user
* Document http classes


* dotenv for filesystem based testing
* complete file iterator tests

# Reflections
* validate parameters in reflection factory / method mapping

# Validation

# Describe
* Describe string, describesCharacters method that lists all characters example: a, space, tab

# HTTP
* allow user to pass options to client

# Collection
* refactor methods to return collection instance instead of array. Test with subclasses

# Laravel
* SetsAttributesTrait
    * define validation / processing rules
    * "array|size:2|->serialize"
    * define __set, use parent __set after processing
