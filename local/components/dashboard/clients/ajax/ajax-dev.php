<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$file_log =  $_SERVER['DOCUMENT_ROOT'] . '/local/components/brokci/dashboard/ajax/logDev.txt';

if (!empty($_REQUEST)) {
	$arParams = $_REQUEST;
	//file_put_contents($file_log, print_r($arParams, true));
	session_start();
	$_SESSION['request'] = serialize($_REQUEST['auth']);
	switch ($arParams['type']) { 
		
			case 'complex': 
			$APPLICATION->IncludeComponent(
				"dev_object:complex",
				"b4",
				$arParams,
				false
			);
			break;
			
			case 'building': 
			$APPLICATION->IncludeComponent(
				"dev_object:building",
				"b4",
				$arParams,
				false
			);
			break;
			
			case 'section': 
			$APPLICATION->IncludeComponent(
				"dev_object:section",
				"b4",
				$arParams,
				false
			);
			break;
			
			case 'chartsMob':
			$APPLICATION->IncludeComponent(
				"dev_report:charts",
				"mobile",
				$arParams,
				false
			);
			break;
			case 'charts':
			$APPLICATION->IncludeComponent(
				"dev_report:charts",
				"destop",
				$arParams,
				false
			);
			break;
			
		case 'favourites':
			$APPLICATION->IncludeComponent(
				"dev_favourites:favouritesCatalogFilter",
				"v1",
				$arParams,
				false
			);
			break;
		case 'objectCom':
			$APPLICATION->IncludeComponent(
				"dev_object:cloudCatalogFilter",
				"new",
				$arParams,
				false
			);
			break;
			case 'chess':
			$APPLICATION->IncludeComponent(
				"dev_object:newCatalogFilter",
				"FilterNew",
				$arParams,
				false
			);
			break;
			
			case 'marketing':
			$APPLICATION->IncludeComponent(
				"dev_marketing:object",
				"b4",
				$arParams,
				false
			);
			break;
			case 'object':
			$APPLICATION->IncludeComponent(
				"dev_object:object",
				"b4",
				$arParams,
				false
			);
			break;
		case 'plan_edit_b4':
			$APPLICATION->IncludeComponent(
				"dev_settings:plan_edit",
				"b4",
				$arParams,
				false
			);
			break;
		case 'plan_type':
			$APPLICATION->IncludeComponent(
				"dev_settings:plan_type",
				"b4",
				$arParams,
				false
			);
			break;

		case 'marketing2':
			$APPLICATION->IncludeComponent(
				"marketing:cloud.general.report",
				"",
				$_REQUEST,
				false
			);
			break;

		case 'marketing_settings':
			$APPLICATION->IncludeComponent(
				"marketing:cloud.install.dev",
				"",
				$_REQUEST,
				false
			);
			break;
		case 'objectPars':
			$APPLICATION->IncludeComponent(
				"dev_object:ParsCatalogFilter",
				"v1",
				$_REQUEST,
				false
			);
			break;
			case 'objectMob':
			$APPLICATION->IncludeComponent(
				"dev_object:cloudCatalogFilter",
				"mobile",
				$_REQUEST,
				false
			);
			break;
		case 'map':
			$APPLICATION->IncludeComponent(
				"map:catalog.geoForCloud",
				"ParsCatalog",
				$_REQUEST,
				false
			);
			break;
		case 'favouritesStage':
			$APPLICATION->IncludeComponent(
				"dev_settings:favouritesStage",
				"b4",
				$_REQUEST,
				false
			);
			break;
			case 'objectStage':
			$APPLICATION->IncludeComponent(
				"dev_settings:objectStage",
				"b4",
				$_REQUEST,
				false
			);
			break;
			case 'support':
			$APPLICATION->IncludeComponent(
				"dev:support",
				"",
				$_REQUEST,
				false
			);
			break;
		case 'general_settings':
			$APPLICATION->IncludeComponent(
				"marketing:general.settings",
				"",
				$_REQUEST,
				false
			);
			break;
			
			break;
			case 'selection':
			$APPLICATION->IncludeComponent(
				"dev_favourites:selection",
				"b4",
				$_REQUEST,
				false
			);
			break;
			case 'galleryUpdate':
			$APPLICATION->IncludeComponent(
				"gallery:gallery_update",
				"",
				$_REQUEST,
				false
			);
			break;
			case 'block_site':
			$APPLICATION->IncludeComponent(
				"dev_settings:block_site",
				"",
				$_REQUEST,
				false
			);
			break;

		default:
			# code... home/bitrix/www/local/components/gallery/galleryUpdate
			break;
			
	}

}


file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);  
