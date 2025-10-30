<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$log = $_SERVER['DOCUMENT_ROOT'] . __DIR__ . '/logAjax.txt';
p($_POST, "start", $log);
$APPLICATION->IncludeComponent(
"support:mcm",
"admin",
$arParams,
false
);