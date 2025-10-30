<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logInstall.txt";
p($_REQUEST, "start", $log);
if($_REQUEST['member_id']) {
    $arParams = $_REQUEST;
    $arParams['app'] = 'scan_doc';
    $arParams['appType'] = 'scan_doc';
    $APPLICATION->IncludeComponent(
        "install:base",
        "scanDoc",
        $arParams,
        false
    );
}