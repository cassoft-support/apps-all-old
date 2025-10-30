<?
define('APP_ID', 'app.605307a7cd0472.77922132'); // app.60c85c2f3ad064.60974923
define('APP_SECRET_CODE', 'adJtnb2paRHU2CUQrhoCordg2Z74QHSbhwywC2T2b81ejG4gNO'); // RGm349Ltb3h0H6uKhRe50DCkK13wdyn3JZ69GYh2bukDmtpebd
define('APP_REG_URL', 'https://city.brokci.ru/pub/forCloud/test/realEstateObject_2/index.php');

require_once('bitrix24.php');
require_once('bitrix24exception.php');
require_once('bitrix24entity.php');
require_once('bitrix24batch.php');
require_once('bitrix24deal.php');
require_once('log.php');
require_once('Hlbk.php'); //класс Руслана для работы с инфоблоками

function prepareFromRequest($arRequest) {
	$arResult = array();
	$arResult['domain'] = $arRequest['DOMAIN'];
	$arResult['member_id'] = $arRequest['member_id'];
	$arResult['refresh_token'] = $arRequest['REFRESH_ID'];
	$arResult['access_token'] = $arRequest['AUTH_ID'];
	
	return $arResult;
}

function prepareFromDB($arAccessParams) {
	$arResult = array();
	$arResult['domain'] = $arAccessParams['PORTAL'];
	$arResult['member_id'] = $arAccessParams['MEMBER_ID'];
	$arResult['refresh_token'] = $arAccessParams['REFRESH_TOKEN'];
	$arResult['access_token'] = $arAccessParams['ACCESS_TOKEN'];
	
	return $arResult;
}

function prepareFromHiBlock($arAccessParams) {
	$arResult = array();
	$arResult['domain'] = $arAccessParams['UF_CS_CLIENT_PORTAL_DOMEN'];
	$arResult['member_id'] = $arAccessParams['UF_CS_CLIENT_PORTAL_MEMBER_ID'];
	$arResult['refresh_token'] = $arAccessParams['UF_CS_CLIENT_PORTAL_REFRESH_TOKEN'];
	$arResult['access_token'] = $arAccessParams['UF_CS_CLIENT_PORTAL_ACCESS_TOKEN'];
	
	return $arResult;
}

function getBitrix24 (&$arAccessData, &$btokenRefreshed, &$errorMessage, $arScope=array()) {
	$btokenRefreshed = null;

	$obB24App = new \Bitrix24\Bitrix24();
	if (!is_array($arScope)) {
		$arScope = array();
	}
	if (!in_array('user', $arScope)) {
		$arScope[] = 'user';
	}
	$obB24App->setApplicationScope($arScope);
	$obB24App->setApplicationId(APP_ID);
	$obB24App->setApplicationSecret(APP_SECRET_CODE);

	// set user-specific settings
	$obB24App->setDomain($arAccessData['domain']);
	$obB24App->setMemberId($arAccessData['member_id']);
	$obB24App->setRefreshToken($arAccessData['refresh_token']);
	$obB24App->setAccessToken($arAccessData['access_token']);
	
	try {
		$resExpire = $obB24App->isAccessTokenExpire();
	}
	catch(\Exception $e) {
		$errorMessage = $e->getMessage();
		// cnLog::Add('Access-expired exception error: '. $error);
	}

	if ($resExpire) {
		// cnLog::Add('Access - expired');
		
		$obB24App->setRedirectUri(APP_REG_URL);

		try {
			$result = $obB24App->getNewAccessToken();
		}
		catch(\Exception $e) {
			$errorMessage = $e->getMessage();
			//\cnLog::Add('getNewAccessToken exception error: '. $error);
		}
		if ($result === false) {
			$errorMessage = 'access denied';
		}
		elseif (is_array($result) && array_key_exists('access_token', $result) && !empty($result['access_token'])) {
			$arAccessData['refresh_token']=$result['refresh_token'];
			$arAccessData['access_token']=$result['access_token'];
			$obB24App->setRefreshToken($arAccessData['refresh_token']);
			$obB24App->setAccessToken($arAccessData['access_token']);
			// \cnLog::Add('Access - refreshed');
			//ключ btokenRefreshed меняется, это можно отследить там, где создается объект obB24App и сделать запись в хайблок
			$btokenRefreshed = true;
		}
		else {
			$errorMessage = 'не удалось получить refresh_token';
			$btokenRefreshed = false;
		}
	}
	else {
		$btokenRefreshed = false;
	}
	return $obB24App;	
}