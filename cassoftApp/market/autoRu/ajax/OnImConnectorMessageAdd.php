<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logMessageAdd.txt";
p($_REQUEST, "start", $log);
$memberId = $_REQUEST['auth']['member_id'];
if ($memberId) {
    $app = "mcm";
    $app_code = "auto_ru";
    $appAccess = 'app_mcm_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchAppID($memberId, $app_code);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        if ($_REQUEST['data']['CONNECTOR'] === 'cs_auto_ru') {
            $arParams = $_REQUEST;
            $arParams['app'] = $app;
            $arParams['app_code'] = $app_code;
            $arParams['tempList'] = 'sendOut';
            $APPLICATION->IncludeComponent(
                "messager:autoRu",
                'desctop',
                $arParams,
                false
            );
        }
    }
}