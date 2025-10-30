<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
//require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once( "/var/www/bitirx-brokci/data/www/app.cassoft.ru/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logMessageAdd.txt";
//p($_REQUEST, "start", $log);
p($_REQUEST, date('c'), $log);
$memberId = $_REQUEST['auth']['member_id'];
if ($memberId) {
    $CloudApp = "mcm";
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        if ($_REQUEST['event'] === 'ONIMCONNECTORMESSAGEADD') {
            $arParams = $_REQUEST;
            $arParams['app'] = $CloudApp;
            $arParams['tempList'] = 'sendOut';
            $APPLICATION->IncludeComponent(
                "messager:mcm",
                'desctop',
                $arParams,
                false
            );
        }
    }
}