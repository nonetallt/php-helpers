* use multibyte safe mb underscore prefixed functions for all string processing
  except files using bytes

* Describe object should be refactored
    * do not use reflection repo
    * move functionality to Strr or new Describe:: class


* ReflectionClassRepository
    * Separate collection from repository class into property
    * Method to manually add mapping into repo ValidationRuleRepository::add(string $alias, ValidationRule $rule)

* ArrayValidator should be able to use user provided ValidationFactory
* ValidatorInterface validate($value, string $name, ValidationSettings $settings) : ValidationResult; implement for all validators
* Closure based validation for ValidationSettings
* Make sure that HttpRequestSetting response processors can be added by user
* Refactor most of http request settings to processor classes since processors are the ones using those settings
* Implement more of guzzlehttp request options as settings
* Document http classes


* dotenv for filesystem based testing
* complete file iterator tests

# Reflections
* validate parameters in reflection factory / method mapping

# Validation
* remove validation rule "createResult" redundant first parameter
* validation rule url https parameter

# Describe
* Describe string, describesCharacters method that lists all characters example: a, space, tab

# HTTP
* allow user to pass options to client

# Collection
* refactor methods to return collection instance instead of array. Test with subclasses

# Json
* require ext-json in composer

# Str
* use mb_string for everything
* require ex-tmbstring in composer

# Laravel
* SetsAttributesTrait
    * define validation / processing rules
    * "array|size:2|->serialize"
    * define __set, use parent __set after processing
