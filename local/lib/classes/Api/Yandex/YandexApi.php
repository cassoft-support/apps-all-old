<?php
namespace Cassoft\Api\Yandex;

class YandexApi {

    public string $token;
    public string $Xtoken;
    public \GuzzleHttp\Client $client;
    public string $feedId;
    public string $link = 'https://api.realty.yandex.net/2.0/crm';

    public function __construct(string $token, string $Xtoken, \GuzzleHttp\Client $client) {
        $this->token = $token; 
        $this->Xtoken = $Xtoken; 
        $this->client = $client; 
    }
    
    public function getFeeds() {
        $method = 'GET'; 
        $linkRequest = "$this->link/feeds";
        return $this->request($method, $linkRequest);
    }
   
    public function getAcceptFeeds() {
        $feeds = $this->getFeeds();
        foreach($feeds['feeds'] as $feed) {
            $find = strpos($feed['status'], 'ACCEPTED'); 
            if($find !== false) {
                $this->feedId = $feed['id'];
            } 
        }
    }

    public function getOffers() {
        $this->getAcceptFeeds();

        if(empty($this->feedId)) {
            return $this->getErrorMessage('Нет валидного фида');
        }

        $method = 'GET'; 
        $linkRequest = "$this->link/offers";
        $config = [
            'feedId' => $this->feedId,
        ];
        return $this->request($method, $linkRequest, $config);
    }

    private function request(string $method, string $linkRequest, $config = false) {
        if(!$config) {
            $config = [];
        }
        $config['headers'] = [
            'Authorization' => "OAuth $this->token",
            'X-Authorization' => $this->Xtoken,
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