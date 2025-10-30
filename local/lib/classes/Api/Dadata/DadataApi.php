<?php

namespace Cassoft\Api\Dadata;

class DadataApi
{
    private string $token;
    private string $secret;
    private array $headers;
    private \GuzzleHttp\Client $GuzzleSuggestions;
    private string $urlSuggestions = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/';



    public function __construct(string $token, ?string $secret = '')
    {
        $this->token = $token;
        $this->secret = $secret;
        $this->setHeaders();
        $this->GuzzleSuggestions = new \GuzzleHttp\Client(
            [
                'headers' => $this->headers,
                'base_uri' => $this->urlSuggestions
            ]
        );
    }
    public function suggestionsAddress(string $address)
    {
        $url = 'suggest/address';
        $body = [
            'query' => $address,
        ];
        $requestType = 'POST';
        return json_decode($this->request($requestType, $url, $body), true);
    }



    private function request(string $requestType, string $url, $body = [])
    {
        $request = $this->GuzzleSuggestions->request($requestType, $url, ['json' => $body]);
        return $request->getBody()->getContents();
    }
    private function setHeaders()
    {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Token ' . $this->token
        ];

        return $this;
    }
}
