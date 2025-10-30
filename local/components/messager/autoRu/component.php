<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/logComp.txt";
p($arParams, date('c'), $log);

$this->IncludeComponentTemplate($arParams["tempList"]);
?>