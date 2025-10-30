<?php

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once('CApplication.php');


//$application = new CApplication();

    $file_log = "/home/bitrix/www/pub/cassoftApp/cloudReceiptsMB/log.txt";
    file_put_contents($file_log, print_r("app\n", true));

//echo "<pre>"; print_r($_REQUEST); echo "</pre>";
    if (!empty($_REQUEST)) {
        file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);

        file_put_contents($file_log, print_r($isAuth, true), FILE_APPEND);


        $arParams["settings"] = $_REQUEST;
        //$arParams['isAdmin'] = $application->checkAdmin();
        //$arParams['user'] = $application->getCurrentUser()['result'];
        if ($_REQUEST['PLACEMENT'] === 'CRM_DEAL_DETAIL_TAB') {
            $APPLICATION->IncludeComponent(
                "pay_system:cloud_receipts_deal",
                "modulBankDeal",
                $arParams,
                false
            );
        } else {
            $APPLICATION->IncludeComponent(
                "pay_system:dashboard",
                "cloud_receipts_mb",
                $arParams,
                false
            );
        }
    }
