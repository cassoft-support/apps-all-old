<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__ . "/logPlacementTabs.txt";

p($_REQUEST, "start", $log);
$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $CloudApp = "mcm";
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0 ) {
        $arParams = $_REQUEST;
        $arParams['app'] = $CloudApp;
       $arParams["tempList"] = 'work';
        $APPLICATION->IncludeComponent(
            "messager:mcm",
            "fields",
            $arParams,
            false
        );
    }
}