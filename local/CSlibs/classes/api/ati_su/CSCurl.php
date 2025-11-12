<?php

    class CSCurl
    {
        public $token;
        public $clientID;

        public function __construct($appToken, $appClientID = false)
        {
            $this->token = $appToken;
            $this->clientID = $appClientID;
        }

        public function callAccount()
        {
            $resToken = $this->token;
            //  $resToken = 'f44d3cb9238b4aa2941216854cac56f8';
            //  $arParams = http_build_query($params);
            $resURL = 'https://api.ati.su/v1.0/account';
            $ch = curl_init($resURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $resToken));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            curl_close($ch);
            $arResult = json_decode($result, true);
            return $arResult;
        }

        public function callEvent($params)
        {
            $resToken = $this->token;
            //  $resToken = 'f44d3cb9238b4aa2941216854cac56f8';
            //  $arParams = http_build_query($params);
            $resURL = 'https://api.ati.su' . $params;
            $ch = curl_init($resURL);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $resToken));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            curl_close($ch);
            $arResult = json_decode($result, true);
            return $arResult;
        }

    }