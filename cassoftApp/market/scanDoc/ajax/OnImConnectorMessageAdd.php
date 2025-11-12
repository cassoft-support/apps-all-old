<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logMessageAdd.txt";
p($_REQUEST, "start", $log);
$memberId = $_REQUEST['auth']['member_id'];
if ($memberId) {
    $CloudApp = "cian_messager";
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        if ($_REQUEST['data']['CONNECTOR'] === 'cs_cian_connector') {
            $arParams = $_REQUEST;
            $arParams['app'] = $CloudApp;
            $arParams['tempList'] = 'sendOut';
            $APPLICATION->IncludeComponent(
                "settings:cian_messager",
                'desctop',
                $arParams,
                false
            );
        }
    }
}