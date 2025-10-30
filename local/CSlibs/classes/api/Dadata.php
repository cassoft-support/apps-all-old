<?php
namespace Citrus\Api;
class Dadata
{
	private $base_url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs";
	private $token;
	private $handle;

	function __construct($token)
	{
		$this->token = $token;
	}

	public function init()
	{
		$this->handle = curl_init();
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Accept: application/json",
			"Authorization: Token " . $this->token
		));
		curl_setopt($this->handle, CURLOPT_POST, 1);
	}

	/**
	 * See https://dadata.ru/api/outward/ for details.
	 */
	public function findById($type, $fields)
	{
		$url = $this->base_url . "/findById/$type";
		return $this->executeRequest($url, $fields);
	}

	private function executeRequest($url, $fields)
	{
		curl_setopt($this->handle, CURLOPT_URL, $url);
		if ($fields != null) {
			curl_setopt($this->handle, CURLOPT_POST, 1);
			curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($fields));
		} else {
			curl_setopt($this->handle, CURLOPT_POST, 0);
		}
		$result = curl_exec($this->handle);
		$result = json_decode($result, true);
		return $result;
	}

	/**
	 * See https://dadata.ru/api/geolocate/ for details.
	 */
	public function geolocate($lat, $lon, $count = 10, $radius_meters = 100)
	{
		$url = $this->base_url . "/geolocate/address";
		$fields = array(
			"lat" => $lat,
			"lon" => $lon,
			"count" => $count,
			"radius_meters" => $radius_meters
		);
		return $this->executeRequest($url, $fields);
	}

	/**
	 * See https://dadata.ru/api/iplocate/ for details.
	 */
	public function iplocate($ip)
	{
		$url = $this->base_url . "/iplocate/address?ip=" . $ip;
		return $this->executeRequest($url, $fields = null);
	}

	/**
	 * See https://dadata.ru/api/suggest/ for details.
	 */
	public function suggest($type, $fields)
	{
		$url = $this->base_url . "/suggest/$type";
		return $this->executeRequest($url, $fields);
	}

	public function close()
	{
		curl_close($this->handle);
	}

	public function getAddressAndFlatByAddress($address) {
		$fields = array("query" => $address, "count" => 1);
		$result = $this->suggest("address", $fields);
		$resultArray = [];
		if ($result['suggestions'][0]) {
			if ($result['suggestions'][0]['value']) {
				$resultAddress = $result['suggestions'][0]['value'];
				$str = strpos($resultAddress, ", кв ");
				if ($str)
					$resultAddress = substr($resultAddress, 0, $str);

				$resultArray['address'] = $resultAddress;
			}
			if($result['suggestions'][0]['data']['flat']) {
				$resultArray['flat'] = $result['suggestions'][0]['data']['flat'];
			}
		}
		return $resultArray;
	}
}
