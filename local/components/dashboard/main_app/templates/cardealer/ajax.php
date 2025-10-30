<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $file_log =  __DIR__ . '/logAjax.txt';
    // file_put_contents($file_log, print_r($_REQUEST, true));

    if (!empty($_REQUEST)) {
        $arParams = $_REQUEST;
       file_put_contents($file_log, print_r($arParams, true));
       file_put_contents($file_log, print_r($_POST, true));
        session_start();
        $_SESSION['request'] = serialize($_REQUEST['auth']);

        switch ($arParams['type']) {
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
            case 'support':
                $APPLICATION->IncludeComponent(
                    "support:cardealer",
                    "admin",
                    $arParams,
                    false
                );
                break;
            case 'general_settings':
                $APPLICATION->IncludeComponent(
                    "settings:base",
                    "cardealer",
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
