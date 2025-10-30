<?php
namespace Cassoft\Api\Cian;

class CianApi {

    public \GuzzleHttp\Client $client;
    public string $token;
    public string $link = 'https://public-api.cian.ru';

    public function __construct(string $token, \GuzzleHttp\Client  $client) {
        $this->client = $client;
        $this->token = $token;
    }
    
    public function getLastReportStatus() {
        $method = 'GET'; 
        $linkRequest = "$this->link/v1/get-last-order-info";
        return $this->request($method, $linkRequest);
    }

    public function getLastReport() {
        $method = 'GET'; 
        $linkRequest = "$this->link/v1/get-order";
        return $this->request($method, $linkRequest);
    }

    private function request(string $method, string $linkRequest, $config = false) {
        if(!$config) {
            $config = [];
        }
        $config['headers'] = [
            'Authorization' => "Bearer $this->token",
        ];
        $response = $this->client->request( $method, $linkRequest, $config);
        $content = $response->getBody()->getContents();
        return json_decode($content, true);
    }
    public function getErrorMessage($message) {
        $error = [];
        $error['status'] = 'error';
        $error['message'] = $message;
        return $error;
    }
}