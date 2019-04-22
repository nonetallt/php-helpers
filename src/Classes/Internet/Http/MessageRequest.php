<?php

namespace App\Domain\Api;

use Nonetallt\Helpers\Arrays\Traits\ConstructedFromArray;
use App\Domain\Messages\GenericMessage;

/**
 * Wrapper class for http request information
 */
class MessageRequest extends HttpRequest
{
    use ConstructedFromArray;

    private $message;

    public function __construct(GenericMessage $message, string $method, string $url, array $query = [])
    {
        $this->message = $message;
        parent::__construct($method, $url, $query);
    }

    public static function arrayValidationRules()
    {
        $class = GenericMessage::class;

        return [
            'message' => "required|subclass_of:$class",
            'method' => 'required|string',
            'url' => 'required|string',
            'query' => 'array'
        ];
    }

    public function getMessage()
    {
        return $this->message;
    }
}
