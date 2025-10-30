<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $file_log = $_SERVER['DOCUMENT_ROOT'] . __DIR__ . '/logAjax.txt';
    // file_put_contents($file_log, print_r($_REQUEST, true));

    if (!empty($_REQUEST)) {
        $arParams = $_REQUEST;
        //file_put_contents($file_log, print_r($arParams, true));
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
                case 'balance':

                $APPLICATION->IncludeComponent(
                    "financier:balance",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'items_debit_credit':
                $APPLICATION->IncludeComponent(
                    "financier:items_debit_credit",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'decode_deb_cred':
                $APPLICATION->IncludeComponent(
                    "financier:decode_deb_cred",
                    "desctop",
                    $arParams,
                    false
                );
                break;
            case 'personal_account':
                $APPLICATION->IncludeComponent(
                    "financier:personal_account",
                    "desctop",
                    $arParams,
                    false
                );
                break;


            case 'support':
                $APPLICATION->IncludeComponent(
                    "support:financier",
                    "admin",
                    $arParams,
                    false
                );
                break;

            case 'general_settings':
                $APPLICATION->IncludeComponent(
                    "settings:base",
                    "financier",
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


            default:
                # code... home/bitrix/www/local/components/gallery/galleryUpdate
                break;
        }
    }


    file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);
