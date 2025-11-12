<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logLeadUpdate.txt";
p($_REQUEST, date('c'), $log);
$memberId = $_REQUEST['auth']['member_id'];
if ($memberId) {
    $CloudApp = "mcm";
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0 && $_REQUEST['event'] === 'ONCRMLEADUPDATE') {
            $arParams = $_REQUEST;
            $arParams['app'] = $CloudApp;
            $arParams['leadId'] = $_REQUEST['data']['FIELDS']['ID'];

            $APPLICATION->IncludeComponent(
                "event:deal_update",
                "mcm",
                $arParams,
                false
            );
        }
    }