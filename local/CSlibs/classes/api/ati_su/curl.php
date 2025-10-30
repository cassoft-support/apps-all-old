<?php

    /**
     * @version 1.2
     */

    class CScurl
    {
        const VERSION = '1.2';
        public $token;

        public function __construct($appToken)
        {
            $this->token = $appToken;
        }

        static function callCurl($key, $method, $params)
        {
            $arParams = http_build_query($params);
            $ch = curl_init($key . $method . '.json?' . $arParams);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            curl_close($ch);
            $arResult = json_decode($result, true);
            return $arResult;
        }
    }