<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$file_log = __DIR__ . "/logInstall.txt";
file_put_contents($file_log, print_r("installDev\n", true));
file_put_contents($file_log, print_r(date('c') . "\n", true), FILE_APPEND);
file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);
$arParams = $_REQUEST;
$arParams['app']='hr_pro';
$arParams['appType']='hr_pro';
$APPLICATION->IncludeComponent(
    "install:base",
    "hr_pro",
    $arParams,
    false
);
