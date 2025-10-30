<?php
namespace Citrus\Api;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class AvitoApi
{
	const AVITO_URL = "https://api.avito.ru/";

	protected $userId = null;
	protected $clientId = null;
	protected $secretId = null;
	protected $accessToken = null;
	protected $tokenType = null;
	private $expiresIn = null;
	protected $errorMessage = "";

	/**
	 * @return null
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}


	/**
	 * AvitoApi constructor.
	 * @param $arParams
	 * @internal param null $userId
	 * @internal param null $clientId
	 * @internal param null $secretId
	 */
	public function __construct($arParams)
	{
		$this->userId = $arParams["userId"];
		$this->clientId = $arParams["clientId"];
		$this->secretId = $arParams["secretId"];
	}

	/**
	 * Авторизация
	 * @return bool
	 */
	protected function Authorize(){
		$data = array(
			"grant_type" => "client_credentials",
			"client_id" => $this->clientId,
			"client_secret" => $this->secretId
		);

		$result = $this->SendRequest("GET", "token", $data, true);
		if(!$result){
			return false;
		}

		$this->accessToken = $result["access_token"];
		$this->tokenType = $result["token_type"];
		$this->expiresIn = mktime() + $result["expires_in"] - 10;

		return true;
	}

	/**
	 * Выполнение запроса
	 * @param string $method
	 * @param string $request
	 * @param array $data
	 * @return mixed
	 */
	public function SendRequest($method = "GET", $request ="" , $data = array(), $auth = false){
		$ch = curl_init();
		if(in_array($method, array("GET", "DELETE"))) {
			curl_setopt_array($ch, array(
				CURLOPT_URL => self::AVITO_URL . $request . "?" . http_build_query($data),
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CUSTOMREQUEST => $method
			));
		}
		else{
			curl_setopt_array($ch , array(
				CURLOPT_URL => self::AVITO_URL . $_REQUEST,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_POSTFIELDS => $data,
			));
		}

		if(false === $auth)
			$this->CheckToken();

		if($this->accessToken !== null){
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				implode(" ", array("authorization:", $this->tokenType, $this->accessToken))
			));
		}

		$result = curl_exec($ch);

		if(!$result){
			$this->errorMessage = curl_error($ch);
			curl_close($ch);
			return false;
		}

		curl_close($ch);

		if($result)
			$result = json_decode($result, true);

		if($result == null){
			$this->errorMessage = "ошибка разбора json";
			return false;
		}

		if(isset($result["error"])){
			$this->errorMessage = $result["error"]["message"];
			return false;
		}

		return $result;
	}

	/**
	 * Проверяет токен (время жизни)
	 * @return bool
	 */
	private function CheckToken() {
		if(mktime() >= $this->expiresIn)
			return $this->Authorize();

		return true;
	}
}