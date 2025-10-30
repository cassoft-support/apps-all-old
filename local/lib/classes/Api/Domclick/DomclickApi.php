<?php
namespace Cassoft\Api\Domclick;

class DomclickApi {
    
    public string $token;
    public string $link;
    public \GuzzleHttp\Client $client;
    
    public function __construct(string $token, string $link, \GuzzleHttp\Client $client) {
        $this->token = $token;
        $this->link = $link;
        $this->client = $client;
    }
    
    public function getLastReport() {
        $response = $this->client->request('GET', $this->link, [
            'headers' => [
                'Authorization' => "Token $this->token",
            ]
        ]);
        $xml = simplexml_load_string($response->getBody(),'SimpleXMLElement',LIBXML_NOCDATA);
        return json_decode(json_encode($xml), true);
    }
    public function getErrorMessage($message): array
    {
        $error = [];
        $error['status'] = 'error';
        $error['message'] = $message;
        return $error;
    }
}