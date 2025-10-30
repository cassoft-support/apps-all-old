<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $file_log = $_SERVER['DOCUMENT_ROOT'] . __DIR__ . '/logFulfillment.txt';
    file_put_contents($file_log, print_r($_REQUEST, true));
//    file_put_contents($file_log, print_r('test', true));

    if (!empty($_REQUEST)) {
        $arParams = $_REQUEST;
        //file_put_contents($file_log, print_r($arParams, true));
        session_start();
        $_SESSION['request'] = serialize($_REQUEST['auth']);
        switch ($arParams['type']) {

            case 'applications':
                $APPLICATION->IncludeComponent(
                    "fulfillment:applications",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'product':
                $APPLICATION->IncludeComponent(
                    "fulfillment:product",
                    "desctop",
                    $arParams,
                    false
                );
                break;
                case 'stage':
                $APPLICATION->IncludeComponent(
                    "fulfillment:appStage",
                    "b4",
                    $arParams,
                    false
                );
                break;

            case 'support':
                $APPLICATION->IncludeComponent(
                    "support:fulfillment",
                    "admin",
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
