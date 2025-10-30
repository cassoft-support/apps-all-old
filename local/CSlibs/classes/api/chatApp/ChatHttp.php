<?php
namespace CSlibs\Api\ChapApp;
class ChatHttp {
	private static $outHeaders;

	/**
	  * Отправляет запрос методом GET.
	  *
	  * @param (string) $url - URL для отправки запроса.
	  * @param (array) $params - массив параметров, которые нужно передать в запросе.
	  * @param (array) $headers - массив заголовков, которые передаются с запросом.
	  *
	  * @return (array) $arrData - массив с данными ответа на запрос.
	*/

	public static function requestGet($url, $params=array(), $headers=array()){
		$arrData = self::request('GET', $url, $params, $headers);

		return $arrData;
	}

	/**
	  * Отправляет запрос методом POST.
	  *
	  * @param (string) $url - URL для отправки запроса.
	  * @param (array) $params - массив параметров, которые нужно передать в запросе.
	  * @param (array) $headers - массив заголовков, которые передаются с запросом.
	  *
	  * @return (array) $arrData - массив с данными ответа на запрос.
	*/

	public static function requestPost($url, $params=array(), $headers=array()){
		$arrData = self::request('POST', $url, $params, $headers);
		
		return $arrData;
	}

	/**
	  * Отправляет запрос методом POST which JSON.
	  *
	  * @param (string) $url - URL для отправки запроса.
	  * @param (array) $params - массив параметров, которые нужно передать в запросе.
	  * @param (array) $headers - массив заголовков, которые передаются с запросом.
	  *
	  * @return (array) $arrData - массив с данными ответа на запрос.
	*/

	public static function requestPostWhichJson($url, $params=array(), $headers=array()){
		$arrData = self::request('POSTWHICHJSON', $url, $params, $headers);
		
		return $arrData;
	}

	/**
	  * Отправляет запрос с помощью CURL.
	  *
	  * @param (string) $typeMethod - вид запроса GET/POST.
	  * @param (string) $url - URL для отправки запроса.
	  * @param (array) $data - массив параметров, которые нужно передать в запросе.
	  * @param (array) $headers - массив заголовков, которые передаются с запросом.
	  *
	  * @return (array) $arrData - массив с данными ответа на запрос.
	*/

	private static function request($typeMethod, $url, $data=array(), $headers=array()){
		$arrData = array();

		$headers[] = 'cache-control: no-cache';
		$headers[] = 'accept: application/json';
		
		if ($data){
			switch ($typeMethod){
				case 'GET':
						$stringParams = http_build_query($data);
						$url .= '?' . $stringParams;
					break;
				case 'POST':
						$postData = http_build_query($data);
					break;
				case 'POSTWHICHJSON':
						$postData = json_encode($data);
					break;
			}
		}

		$curl = curl_init();

		$curlParams = array(
			CURLOPT_URL =>  $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_NOBODY => false,
			CURLOPT_HTTPHEADER => $headers,
			//CURLINFO_HEADER_OUT => true,
			//CURLOPT_HEADER => true,
			CURLOPT_POST => ($typeMethod == 'POST' or $typeMethod == 'POSTWHICHJSON') ? true : false,
		);

		if ($typeMethod == 'POST' or $typeMethod == 'POSTWHICHJSON')
			$curlParams[CURLOPT_POSTFIELDS] = $postData;

		curl_setopt_array($curl, $curlParams);
		
		$response = curl_exec($curl);

		self::$outHeaders = curl_getinfo($curl);
		
		curl_close($curl);

		if ($response)
			$arrData = json_decode($response, true);

		return $arrData;
	}


	public static function getOutHeaders(){
		return self::$outHeaders;
	}
}
