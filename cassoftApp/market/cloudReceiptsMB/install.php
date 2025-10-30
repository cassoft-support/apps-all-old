<?php

    define("NO_KEEP_STATISTIC", true);
    define("NOT_CHECK_PERMISSIONS", true);
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    //$file_log="/home/bitrix/www/cassoftApp/cassoft/cassoftBase/logIndex.txt";
    $file_log = __DIR__ . "/logIndex.txt";
    file_put_contents($file_log, print_r("index4\n", true));
    file_put_contents($file_log, print_r($lile . "\n", true), FILE_APPEND);
    file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);
    $arParams = $_REQUEST;
    $APPLICATION->IncludeComponent(
        "install:base",
        "cloud_receipts_mb",
        $arParams,
        false
    );
