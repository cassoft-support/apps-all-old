<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__ . "/logCianBot.txt";
p($_REQUEST, "start", $log);
$memberId = $_REQUEST['auth']['member_id'];
if ($memberId) {
    $CloudApp = "cian_messager";
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        if ($_REQUEST['event'] === 'ONIMBOTMESSAGEADD') {

            $arParams = $_REQUEST;
            $arParams['app'] = $CloudApp;
            $arParams['tempList'] = 'messageAdd';
            $APPLICATION->IncludeComponent(
                "bots:cian_bots",
                'desctop',
                $arParams,
                false
            );
        }
    }
}