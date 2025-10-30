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

            case 'support':
                $APPLICATION->IncludeComponent(
                    "support:gas_mix",
                    "admin",
                    $arParams,
                    false
                );
                break;
case 'charts':
                $APPLICATION->IncludeComponent(
                    "report:chartsNew",
                    "desktop",
                    $arParams,
                    false
                );
                break;

            case 'general_settings':
                $APPLICATION->IncludeComponent(
                    "settings:base",
                    "gas_mix",
                    $arParams,
                    false
                );
                break;
            case 'candidates':
                $APPLICATION->IncludeComponent(
                    "hr:candidates",
                    "",
                    $arParams,
                    false
                );
                break;
            case 'vacancy':
                $APPLICATION->IncludeComponent(
                    "hr:vacancy",
                    "",
                    $arParams,
                    false
                );
                break;
            case 'stageCandidates':
                $APPLICATION->IncludeComponent(
                    "hr:stageCandidates",
                    "",
                    $arParams,
                    false
                );
                break;
            case 'transferStat':
                $APPLICATION->IncludeComponent(
                    "report:lid_transfer",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'transferGroup':
                $APPLICATION->IncludeComponent(
                    "settings:group_transfer",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'algorithm':
                $APPLICATION->IncludeComponent(
                    "settings:algorithm_transfer",
                    "desctop",
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
