* dotenv for filesystem based testing
* complete file iterator tests
* csv file
    * auto-detect delimiter





# Reflections
* validate parameters in reflection factory / method mapping

# Validation

# Describe
* Describe string, describesCharacters method that lists all characters example: a, space, tab

# HTTP
* allow user to pass options to client
* status codes
    * use status code for request error when applicable (so that the error can be used to know wether user should attempt to retry request)


# Collection
* refactor methods to return collection instance instead of array. Test with subclasses

# Laravel
* SetsAttributesTrait
    * define validation / processing rules
    * "array|size:2|->serialize"
    * define __set, use parent __set after processing
