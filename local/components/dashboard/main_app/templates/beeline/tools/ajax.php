<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$file_log =  __DIR__ . '/logAjax.txt';

if (!empty($_REQUEST)) {
	$arParams = $_REQUEST;
	file_put_contents($file_log, print_r($arParams, true));
	file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);
	session_start();
	$_SESSION['request'] = serialize($_REQUEST['auth']);
	switch ($arParams['type']) {
		case 'dashboard':
			$APPLICATION->IncludeComponent(
				"telephony:dashboard",
				"beeline",
				$arParams,
				false
			);
			break;
		case 'authSettings':
			$APPLICATION->IncludeComponent(
				"telephony:authSettings",
				"beeline",
				$arParams,
				false
			);
			break;
            case 'support':
			$APPLICATION->IncludeComponent(
				"support:beeline",
				"admin",
				$arParams,
				false
			);
			break;
		case 'settings':
			$APPLICATION->IncludeComponent(
				"telephony:settings",
				"beeline",
				$arParams,
				false
			);
			break;
		
		default:
			# code... home/bitrix/www/local/components/gallery/galleryUpdate
			break;
	}
}



//file_put_contents($file_log, print_r( "2",true), FILE_APPEND);
//file_put_contents($f