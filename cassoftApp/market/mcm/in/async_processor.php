<?php
 define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
include $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
$log = __DIR__."/logPro.txt";

$arParams = json_decode(file_get_contents("php://input"), true);
p($arParams , "arParams", $log);

if ($arParams['member_id']) {
//
    $appAccess = "app_mcm_access";
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchAppID($arParams['member_id'], "auto_ru");
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        $APPLICATION->IncludeComponent(
            "messager:autoRu",
            "desctop",
            $arParams,
            false
        );

    }
}