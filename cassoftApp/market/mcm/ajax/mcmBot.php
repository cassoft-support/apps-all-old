<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__ . "/logMcmBot.txt";
p($_REQUEST, date('c'), $log);
$memberId = $_REQUEST['auth']['member_id'];
if ($memberId) {
$CloudApp = "mcm";
$appAccess = 'app_' . $CloudApp . '_access';
$HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
$clientsApp = $HlClientApp->searchID($memberId);
p($clientsApp, "rest", $log);
if ($clientsApp["ID"] > 0) {
    $arParams = $_REQUEST;
    $arParams['app'] = $CloudApp;
    $chatEntityId = explode("|", $_REQUEST['data']['PARAMS']['CHAT_ENTITY_ID']);
    if ($chatEntityId === 'cs_auto_ru') {
        $arParams['tempList'] = 'messageAddAuto';
    }else {
        $arParams['tempList'] = 'messageAdd';
    }
        $APPLICATION->IncludeComponent(
            "bots:mcm",
            'desctop',
            $arParams,
            false
        );
}
}