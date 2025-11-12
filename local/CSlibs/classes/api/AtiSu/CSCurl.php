<?php
namespace Api\AtiSu;

    class CSCurl
    {
        public $token;
        const URL = 'https://api.ati.su/v2/';

        public $clientID;

        public function __construct($appToken, $appClientID = false)
        {
            $this->token = $appToken;
            $this->clientID = $appClientID;
        }

        public function sendPostRequest($data, $action)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, self::URL.$action);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $headers = array();
            $headers[] = 'Authorization: Bearer '.$this->token;
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $arRes= 'Error:' . curl_error($ch);
            } else {
                $arRes= $result;
            }
            curl_close($ch);
            $arResult = json_decode($arRes, true);
            return $arResult;
        }

        public function sendPutRequest($data, $action)
        {
            file_put_contents(__DIR__.'/logCurl.txt', print_r($action, true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::URL.$action);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $headers = array();
            $headers[] = 'Authorization: Bearer '.$this->token;
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $arRes= 'Error:' . curl_error($ch);
            } else {
                $arRes= $result;
            }
            curl_close($ch);
            $arResult = json_decode($arRes, true);
            return $arResult;
        }
        public function sendPutRenew($number)
        {
            $resURL = 'https://api.ati.su/v1.0/loads/'.$number.'/renew';
            file_put_contents(__DIR__.'/logPutRenew.txt', print_r($number, true));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $resURL);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
           // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $headers = array();
            $headers[] = 'Authorization: Bearer '.$this->token;
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $arRes= 'Error:' . curl_error($ch);
            } else {
                $arRes= $result;
            }
            curl_close($ch);
            $arResult = json_decode($arRes, true);
            file_put_contents(__DIR__.'/logPutRenew.txt', print_r($arRes, true), FILE_APPEND);
            return $arResult;
        }

        public function callAccount()
        {

            $resURL = 'https://api.ati.su/v1.0/account';
            $ch = curl_init($resURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $arRes= 'Error:' . curl_error($ch);
            } else {
                $arRes= $result;
            }
            curl_close($ch);
            $arResult = json_decode($arRes, true);
            return $arResult;
        }

        public function callEvent($params)
        {
            file_put_contents(__DIR__.'/logCallEvent.txt', print_r($params, true), FILE_APPEND);
            $resURL = 'https://api.ati.su' . $params;
            $ch = curl_init($resURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $arRes= 'Error:' . curl_error($ch);
            } else {
                $arRes= $result;
            }
            curl_close($ch);
            $arResult = json_decode($arRes, true);
          //  file_put_contents(__DIR__.'/logCallEvent.txt', print_r($arRes, true), FILE_APPEND);
            return $arResult;
        }
        public function dellEvent($params)
        {
            $resURL = 'https://api.ati.su/v1.0/loads/' . $params;
            $ch = curl_init($resURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            curl_close($ch);
            file_put_contents(__DIR__.'/logCurl.txt', print_r($result, true));
            $arResult = json_decode($result, true);
            return $arResult;
        }

        public function boardsList()
        {
            $resURL = 'https://api.ati.su//v2/boards/public/boards/list';
            $ch = curl_init($resURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            curl_close($ch);
            $arResult = json_decode($result, true);
            $arResultFull = [];
            foreach ($arResult as $value){
                $arResultFull[] = [
                    'name' => $value['name'],
                    'id' => $value['id']
                ];
            }
            return $arResultFull;
        }
    }



