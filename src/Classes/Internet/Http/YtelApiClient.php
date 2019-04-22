<?php

namespace App\Domain\Api;

use App\DidGroup;
use Nonetallt\Helpers\Arrays\TypedArray;
use Illuminate\Database\Eloquent\Collection;

class YtelApiClient extends HttpClient
{
    public function __construct(string $sid, string $token)
    {
        parent::__construct(3, 10, 10, 10);
        $this->setAuth($sid, $token);
    }

    public function getUsage()
    {
        $method = 'post';
        $url = 'https://api.ytel.com/api/v3/usage/listusage.json';

        return $this->sendRequest(new HttpRequest($method, $url));
    }

    public function sendSmsMessage(string $from, string $to, string $body, string $method = 'get', string $cb = null, bool $smartsms = false, bool $deliveryStatus = false)
    {
        $method = 'post';
        $url = 'https://api.ytel.com/api/v3/sms/sendsms.json';

        $data = [
            'From' => $from,
            'To' => $to,
            'Body' => $body,
            'Method' => $method,
            'MessageStatusCallback' => $cb,
            'Smartsms' => $smartsms,
            'DeliveryStatus' => $deliveryStatus
        ];

        return $this->sendRequest(new HttpRequest($method, $url, $data));
    }

    public function sendSmsMessages(array $messages)
    {
        $messages = TypedArray::create(YtelSmsMessage::class, $messages);

        $method = 'post';
        $url = 'https://api.ytel.com/api/v3/sms/sendsms.json';

        $requests = new HttpRequestCollection();

        /* Create asynchronous requests for each message */
        foreach($messages as $index => $message) {
            $requests->push(new MessageRequest($message->getOriginalMessage(), $method, $url, $message->toArray()));
        }

        return $this->sendRequests($requests);
    }

    public function releaseNumbers(array $numbers)
    {
        $method = 'post';
        $url = 'https://api.ytel.com/api/v3/incomingphone/massreleasenumber.json';
        $requests = new HttpRequestCollection();

        /* ~100 is the apparent max per request */
        foreach(array_chunk($numbers, 100) as $numberChunk) {
            $numberList = implode(',', $numberChunk);
            $query = [ 'PhoneNumber' => $numberList ];
            $requests->push(new HttpRequest($method, $url, $query));
        }

        return $this->sendRequests($requests);
    }

    public function updateNumbers(array $numbers, array $options)
    {
        $method = 'post';
        $url = 'https://api.ytel.com/api/v3/incomingphone/massupdatenumber.json';
        $requests = new HttpRequestCollection();

        /* ~100 is the apparent max per request */
        foreach(array_chunk($numbers, 100) as $numberChunk) {
            $numberList = implode(',', $numberChunk);
            $query = array_merge([ 'PhoneNumber' => $numberList ], $options);
            $requests->push(new HttpRequest($method, $url, $query));
        }

        return $this->sendRequests($requests);
    }

    public function initializeNumbers(Collection $dids, string $cbMethod, string $cbUrl)
    {
        $method = 'post';
        $url = 'https://api.ytel.com/api/v3/incomingphone/massupdatenumber.json';
        $requests = new HttpRequestCollection();

        /* Get did numbers from collection */
        $numbers = [];
        foreach($dids as $did) {
            $numbers[$did->id] = $did->number;
        }
        
        /* ~100 is the apparent max per request */
        foreach(array_chunk($numbers, 100, true) as $numberChunk) {
            $numberList = implode(',', $numberChunk);
            $query = [
                'PhoneNumber' => $numberList,
                'SmsMethod' => $cbMethod,
                'SmsUrl' => $cbUrl,
                'VoiceMethod' => env('YTEL_VOICE_METHOD'),
                'VoiceUrl' => env('YTEL_VOICE_URL'),
            ];
            $request = new HttpRequest($method, $url, $query);
            $request->addExtra('numbers', $numberChunk);
            $requests->push($request);
        }

        return $this->sendRequests($requests);
    }

    public function registerCallbackUrl(string $number, string $method, string $url)
    {
        $method = 'post';
        $url = 'https://api.ytel.com/api/v3/incomingphone/updatenumber.json';

        $query = [
            'PhoneNumber' => $number,
            'SmsMethod' => $method,
            'SmsUrl' => $url
        ];

        return $this->sendRequest(new HttpRequest($method, $url, $query));
    }

    /**
     * @override
     */
    protected function createResponse(HttpRequest $request, \GuzzleHttp\Psr7\Response $response)
    {
        $response = new JsonApiResponse($request, $response);
        $response->setErrorAccessors('Message360->Errors->Error', 'Message');
        return $response;
    }
}
