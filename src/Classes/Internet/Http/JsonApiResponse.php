<?php

namespace App\Domain\Api;

use Nonetallt\Helpers\Templating\RecursiveAccessor;
use App\Domain\Messages\GenericMessage;
use Nonetallt\Helpers\Describe\DescribeObject;
use Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException;

class JsonApiResponse extends HttpResponse
{
    private $decoded;
    private $errorAccessor;
    private $errorMessageAccessor;
    private $originalRequest;

    private function decodeResponse(string $body)
    {
        $decoded = json_decode($body, true);

        if(is_null($decoded)) {
            $error = JsonParsingException::ERROR_MESSAGES[json_last_error()];
            $this->addError("Could not parse JSON response ($error)");
            return [];
        }

        return $decoded;
    }

    public function setErrorAccessors(string $error, string $message = null)
    {
        $this->errorAccessor = $error;
        $this->errorMessageAccessor = $message;
    }

    /**
     * @override
     */
    public function getErrors()
    {
        $accessor = new RecursiveAccessor('->');

        if(is_null($this->errorAccessor)) return $this->errors;
        if(! $accessor->isset($this->errorAccessor, $this->getDecoded())) return $this->errors;

        /* For example, error can have attribute 'message' */
        $errors = $accessor->getNestedValue($this->errorAccessor, $this->getDecoded());

        if(! is_null($this->errorMessageAccessor)) {
            $errors = array_map(function($error) use($accessor){
                return $accessor->getNestedValue($this->errorMessageAccessor, $error);
            }, $errors);
        }

        if(is_string($errors)) $errors = [$errors];
        elseif(is_array($errors)) $errors = array_merge($this->errors, $errors);
        else {
            $given = (new DescribeObject($errors))->describeType();
            throw new \Exception("Unexpected value returned for errors $given");
        }

        return $errors;
    }

    public function getDecoded()
    {
        if(is_null($this->decoded)) $this->decoded = $this->decodeResponse($this->getBody());
        return $this->decoded;
    }
}
