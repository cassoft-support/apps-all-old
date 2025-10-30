<?php
namespace Cassoft\Api\Avito;

class AvitoApi {
    private string $client_id;
    private string $client_secret;
    private string $grant_type;
    private \GuzzleHttp\Client $client;
    private string $token;
    private string $token_type;
    private string $userId;
    private string $link = 'https://api.avito.ru';

    public function __construct (array $params, \GuzzleHttp\Client $guzzle) {
        $this->client_id = $params['client_id']; 
        $this->client_secret = $params['client_secret']; 
        $this->grant_type = 'client_credentials'; 
        $this->client = $guzzle; 
    }

    public function autorizate() {
      file_put_contents("/var/www/www-root/data/www/brokci.cassoft.ru/local/lib/classes/Api/Avito/logAvito.txt", print_r("authAvito\n", true));
        $method = 'GET';
        $linkRequest = "$this->link/token/"; 
        $config = [
            'query' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'grant_type' => $this->grant_type,
            ]
        ];
     //   $cl=$this->client;
      //  file_put_contents("/var/www/www-root/data/www/brokci.cassoft.ru/local/lib/classes/Api/Avito/logAvito.txt", print_r($cl, true), FILE_APPEND);
        $response = $this->client->request($method, $linkRequest, $config);
        file_put_contents("/var/www/www-root/data/www/brokci.cassoft.ru/local/lib/classes/Api/Avito/logAvito.txt", print_r($response, true), FILE_APPEND);
        return json_decode($response->getBody()->getContents(), true);
    }
    
    public function getLastReport() {
        $method = 'GET';
        $linkRequest = "$this->link/autoload/v1/accounts/$this->userId/reports/last_report/";
        return $this->request($method, $linkRequest);
    }

    public function getReportsList() {
        $method = 'GET';
        $linkRequest = "$this->link/autoload/v1/accounts/$this->userId/reports/";
        return $this->request($method, $linkRequest);
    }
    public function getReportById($id) {
        if(empty($id)) {
            return $this->getErrorMessage('Нет id фида');
        }
        $method = 'GET';
        $linkRequest = "$this->link/autoload/v1/accounts/$this->userId/reports/$id";
        return $this->request($method, $linkRequest);
    }

    public function getUserInfo() {
        $method = 'GET'; 
        $linkRequest = "$this->link/core/v1/accounts/self";
        return $this->request($method, $linkRequest);
    }

    public function getAds() {
        $method = 'GET';
        $linkRequest = "$this->link/core/v1/items";
        return $this->request($method, $linkRequest);
    }

    protected function request(string $method, string $linkRequest, $config = false) {
      file_put_contents("/var/www/www-root/data/www/brokci.cassoft.ru/local/lib/classes/Api/Avito/logAvito.txt", print_r("request\n", true), FILE_APPEND);
        if(!$config) {
            $config = [];
        }
        $t=$this->token;
        file_put_contents("/var/www/www-root/data/www/brokci.cassoft.ru/local/lib/classes/Api/Avito/logAvito.txt", print_r($t, true), FILE_APPEND);
        if(empty($this->token) || empty($this->token_type)) {
            return $this->getErrorMessage('Не пройдена авторизация на авито');
        }
        $config['headers'] = [
            'Authorization' => "$this->token_type $this->token"
        ];
        $response = $this->client->request($method, $linkRequest, $config);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getErrorMessage($message): array
    {
        $error = [];
        $error['status'] = 'error';
        $error['message'] = $message;
        return $error;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getTokenType(): string
    {
        return $this->token_type;
    }

    public function setTokenType(string $token_type): void
    {
        $this->token_type = $token_type;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
}
