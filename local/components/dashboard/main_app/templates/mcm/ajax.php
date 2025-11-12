<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $log = $_SERVER['DOCUMENT_ROOT'] . __DIR__ . '/logAjax.txt';
p($_POST, "start", $log);
    if (!empty($_REQUEST)) {
        $arParams = $_REQUEST;
        //file_put_contents($file_log, print_r($arParams, true));
        session_start();
        $_SESSION['request'] = serialize($_REQUEST['auth']);

        switch ($arParams['type']) {



            case 'support':
                $APPLICATION->IncludeComponent(
                    "support:mcm",
                    "admin",
                    $arParams,
                    false
                );
                break;

            case 'general_settings':
                $APPLICATION->IncludeComponent(
                    "settings:mcm",
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



