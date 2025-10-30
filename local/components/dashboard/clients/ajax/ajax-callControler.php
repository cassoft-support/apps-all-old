<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $file_log = $_SERVER['DOCUMENT_ROOT'] . '/local/components/dashboard/main_app/ajax/logCallControler.txt';
    file_put_contents($file_log, print_r($_REQUEST, true));
    if (!empty($_REQUEST)) {
        $arParams = $_REQUEST;
        $arParams['member_id'] = $_REQUEST['auth']['member_id'];
        file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);
        switch ($arParams['type']) {
            case 'chartsMob':
                $APPLICATION->IncludeComponent(
                    "callControler:charts",
                    "mobile",
                    $arParams,
                    false
                );
                break;

            case 'charts':
                $APPLICATION->IncludeComponent(
                    "callControler:charts",
                    "destop",
                    $arParams,
                    false
                );
                break;
            case 'setting':
                $APPLICATION->IncludeComponent(
                    "callControler:setting",
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
