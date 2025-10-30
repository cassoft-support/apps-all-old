<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logCallback.txt";

$data = file_get_contents('php://input');
parse_str($data, $resultData);

p($data, "date", $log);
p($resultData, "resultData", $log);
$memberId = $resultData['client_id'];
p($memberId, "memberId", $log);
if ($memberId) {
    $CloudApp = "mcm";
    $appAccess = "app_mcm_access";
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        $arParams["base"] = $resultData;
        $arParams["tempList"] = "callback";
        $arParams["app"] = $CloudApp;
        $arParams["member_id"] = $memberId;
        $APPLICATION->IncludeComponent(
            "messager:mcm",
            "desctop",
            $arParams,
            false
        );
    }
}



