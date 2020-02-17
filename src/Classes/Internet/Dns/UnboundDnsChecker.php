<?php

namespace Nonetallt\Helpers\Internet\Dns;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class UnboundDnsChecker extends DnsChecker  
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10
        ]);
    }

    public static function getSupportedRecordTypes()
    {
        return [
            'CAA',
            'A',
            'AAAA',
            'TXT'
        ];
    }

    protected function executeQuery(string $domain, string $type = null)
    {
        if(is_null($type)) {
            throw new \InvalidArgumentException("This checker does not support null record types, sorry.");
        }

        $method = 'GET';
        $url = 'https://unboundtest.com/q';
        $query = [
            'type' => $type,
            'qname' => $domain
        ];

        try {

            $response = $this->client->request($method, $url, [
                'query' => $query
            ]);

            return (string)$response->getBody();
        }
        catch(ConnectionException $e) {
            $msg = $e->getMessage();
            throw new DnsCheckException("Connection to dns checker service failed with message: $msg");
        }
    }
}
