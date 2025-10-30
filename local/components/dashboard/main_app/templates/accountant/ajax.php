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

            case 'patents':

                $APPLICATION->IncludeComponent(
                    "accountant:patents",
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


            case 'support':
                $APPLICATION->IncludeComponent(
                    "support:accountant",
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
                    "accountant:stageApps",
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
