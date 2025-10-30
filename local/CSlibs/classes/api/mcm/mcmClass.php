<?php

namespace CSlibs\Api\MCM;

class mcmClass
{
    private $token;
    private $fileLog;
    private $date;


    public function __construct($token)
    {
        $this->token = $token;
        $this->fileLog = __DIR__ . "/logMultiMes.txt";
        $this->date = date("d.m.YTH:i:s");

    }

    public function curlPost($method, $params, $profileId=''){


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wappi.pro/api/'.$method.'?profile_id='.$profileId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$this->token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }
    public function curlGet($method, $profileId='')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wappi.pro/api/'.$method.'?profile_id='.$profileId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$this->token
            ),
        ));

        $response = curl_exec($curl);
$res = json_decode($response, true);
        curl_close($curl);
        return $res;
    }
}