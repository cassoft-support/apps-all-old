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
            case 'portfolio':

                $APPLICATION->IncludeComponent(
                    "stayproperty:portfolio",
                    "desctop",
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
                    "support:stayproperty",
                    "admin",
                    $arParams,
                    false
                );
                break;

            case 'general_settings':
                $APPLICATION->IncludeComponent(
                    "settings:base",
                    "stayproperty",
                    $arParams,
                    false
                );
                break;
            case 'stageApps':
                $APPLICATION->IncludeComponent(
                    "stayproperty:stageApps",
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
