<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $file_log = $_SERVER['DOCUMENT_ROOT'] . __DIR__ . '/logLogistics.txt';
    // file_put_contents($file_log, print_r($_REQUEST, true));

    if (!empty($_REQUEST)) {
        $arParams = $_REQUEST;
        //file_put_contents($file_log, print_r($arParams, true));
        session_start();
        $_SESSION['request'] = serialize($_REQUEST['auth']);
        switch ($arParams['type']) {
            case 'reportApplication':

                $APPLICATION->IncludeComponent(
                    "logistics:reportAp",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'cars':

                $APPLICATION->IncludeComponent(
                    "logistics:cars",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'applications':

                $APPLICATION->IncludeComponent(
                    "logistics:applications",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'applicationsTab':

                $APPLICATION->IncludeComponent(
                    "logistics:applications",
                    "tab",
                    $arParams,
                    false
                );
                break;
            case 'reportInvoiceOut':
                $APPLICATION->IncludeComponent(
                    "logistics:reportInvoiceOut",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'reportDeal':
                $APPLICATION->IncludeComponent(
                    "logistics:reportDeal",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'update':
                $APPLICATION->IncludeComponent(
                    "install:logistics_pro",
                    "update",
                    $arParams,
                    false
                );
                break;
            case 'charts':
                $APPLICATION->IncludeComponent(
                    "report:logistics_pro",
                    "",
                    $arParams,
                    false
                );
                break;
            case 'support':
                $APPLICATION->IncludeComponent(
                    "support:logistics_pro",
                    "admin",
                    $arParams,
                    false
                );
                break;
                case 'csv':
                $APPLICATION->IncludeComponent(
                    "support:logistics_pro",
                    "csv",
                    $arParams,
                    false
                );
                break;
            case 'request':
                $APPLICATION->IncludeComponent(
                    "logistics:request",
                    "admin",
                    $arParams,
                    false
                );
                break;
            case 'general_settings':
                $APPLICATION->IncludeComponent(
                    "settings:base",
                    "logistics_pro",
                    $arParams,
                    false
                );
                break;
            case 'stageApps':
                $APPLICATION->IncludeComponent(
                    "logistics:stageApps",
                    "b4",
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
