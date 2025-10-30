<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$file_log =  __DIR__ . '/logAjax.txt';
file_put_contents($file_log, print_r(date("d.m.Y H:s"), true));
file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);
file_put_contents($file_log, print_r($_POST, true), FILE_APPEND);
if (!empty($_REQUEST)) {
	$arParams = $_REQUEST;
	//$arParams['member_id']= $_REQUEST['auth']['member_id'];
	//$arParams['app']= "brokci";
	file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);
	session_start();
	$_SESSION['request'] = serialize($_REQUEST['auth']);
	switch ($arParams['type']) {
		case 'chartsMob':
			$APPLICATION->IncludeComponent(
				"brokci_report:charts",
				"mobile",
				$arParams,
				false
			);
			break;
			case 'test':
			$APPLICATION->IncludeComponent(
				"test:test",
				"",
				$arParams,
				false
			);
			break;
        case 'support':
            $APPLICATION->IncludeComponent(
                "support:brokci",
                "admin",
                $arParams,
                false
            );
            case 'messager':
            $APPLICATION->IncludeComponent(
                "massenger:messager",
                "desctop",
                $arParams,
                false
            );
            break;
            case 'transfer':
            $APPLICATION->IncludeComponent(
                "support:transfer",
                "admin",
                $arParams,
                false
            );
            break;
      
      case 'mUp':
			$APPLICATION->IncludeComponent(
				"brokci_settings:marketing_update",
				"",
				$arParams,
				false
			);
			break;
		case 'charts':
			$APPLICATION->IncludeComponent(
				"brokci_report:charts",
				"destop",
				$arParams,
				false
			);
			break;
		case 'favourites':
			$APPLICATION->IncludeComponent(
//				"brokci_favourites:favouritesCatalogFilter",
//				"v1",
                "Favourites:favouritesCatalogFilter",
                "desctop",
				$arParams,
				false
			);
			break;
            case 'favouritesNew':
			$APPLICATION->IncludeComponent(
				"Favourites:favouritesCatalogFilter",
				"v2",
				$arParams,
				false
			);
			break;
		case 'object':
			$APPLICATION->IncludeComponent(
//				"brokci_object:cloudCatalogFilter",
//				//"new",
//				"selectRelevant",
                "catalog_object:CatalogFilter",
                "desktop",
				$arParams,
				false
			);
			break;
    case 'objectNEW':
        $APPLICATION->IncludeComponent(
            "catalog_object:CatalogFilter",
            "desktop",
            $arParams,
            false
        );
      break;
		case 'plan_edit_b4':
			$APPLICATION->IncludeComponent(
				"brokci_settings:plan_edit",
				"b4",
				$arParams,
				false
			);
			break;
			case 'plan_exec':
			$APPLICATION->IncludeComponent(
				"brokci_settings:plan_exec",
				"b4",
				$arParams,
				false
			);
			break;
		case 'plan_type':
			$APPLICATION->IncludeComponent(
				"brokci_settings:plan_type",
				"b4",
				$arParams,
				false
			);
			break;
		case 'marketing':
			$APPLICATION->IncludeComponent(
				"marketing:cloud.general.report.brokci",
				"",
                $arParams,
				false
			);
			break;

		case 'marketing_settings':
			$APPLICATION->IncludeComponent(
				"marketing:setup_marketing",
				"brokci",
                $arParams,
				false
			);
			break;
			case 'adsSchedule':
			$APPLICATION->IncludeComponent(
				"brokci_settings:AdsSchedule",
				"b4",
                $arParams,
				false
			);
			break;
      case 'update':
			$APPLICATION->IncludeComponent(
				"brokci_settings:update",
				"",
                $arParams,
				false
			);
			break;
		case 'objectPars': 
			$APPLICATION->IncludeComponent(
//				"brokci_object:ParsCatalogFilter",
//				"v1",
                "catalog_object:pars.filter",
                "desktop",
                $arParams,
				false
			);
			break;
        case 'objectParsNEW':
            $APPLICATION->IncludeComponent(
                "catalog_object:pars.filter",
                "desktop",
                $arParams,
                false
            );
            break;
            case 'objectParsNEW2':
            $APPLICATION->IncludeComponent(
                "catalog_object:ParsCatalogFilter",
                "desktop",
                $arParams,
                false
            );
            break;
		case 'objectMob':
			$APPLICATION->IncludeComponent(
				"brokci_object:cloudCatalogFilter",
				"mobile",
                $arParams,
				false
			);
			break;
		case 'map':
			$APPLICATION->IncludeComponent(
				"map:catalog.geoForCloud",
				"ParsCatalog",
                $arParams,
				false
			);
			break;
		case 'favouritesStage':
			$APPLICATION->IncludeComponent(
				"settings:favouritesStage",
				"b4",
                $arParams,
				false
			);
			break;
      case 'setupSite':
			$APPLICATION->IncludeComponent(
				"brokci_settings:setup_site",
				"b4",
                $arParams,
				false
			);
			break;
		case 'general_settings':
			$APPLICATION->IncludeComponent(
				"settings:base",
				"brokci",
                $arParams,
				false
			);
			break;
		case 'settings':
			$APPLICATION->IncludeComponent(
				"brokci:dashboard-admin",
				"",
                $arParams,
				false
			);
			break;
		case 'selection':
			$APPLICATION->IncludeComponent(
				"Favourites:selection",
				"b4",
                $arParams,
				false
			);
			break;
            case 'selectionNEW':
			$APPLICATION->IncludeComponent(
				"Favourites:selection",
				"b4",
                $arParams,
				false
			);
			break;
		case 'galleryUpdate':
			$APPLICATION->IncludeComponent(
				"gallery:gallery_update",
				"",
                $arParams,
				false
			);
			break;
			case 'objectEdit':
			$APPLICATION->IncludeComponent(
				"brokci_object:object",
				"b4",
				$arParams,
				false
			);
			break;
			case 'block_site':
			$APPLICATION->IncludeComponent(
				"brokci_settings:block_site",
				"",
                $arParams,
				false
			);
			break;
		default:
			# code... home/bitrix/www/local/components/gallery/galleryUpdate
			break;
	}
}


file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);  
//file_put_contents($file_log, print_r( "2",true), FILE_APPEND);
//file_put_contents($f home/bitrix/www/local/components//