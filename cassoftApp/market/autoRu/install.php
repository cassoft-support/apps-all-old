<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logInstall.txt";
p($_REQUEST, "start", $log);
if($_REQUEST['member_id']) {
    $arParams = $_REQUEST;
    $arParams['app'] = 'cian_messager';
    $arParams['appType'] = 'cian_messager';
    $APPLICATION->IncludeComponent(
        "install:base",
        "cianMessager",
        $arParams,
        false
    );
}