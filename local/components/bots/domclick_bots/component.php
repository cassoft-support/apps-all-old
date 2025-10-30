<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/logComp.txt";
p($arParams, 'start', $log);

$this->IncludeComponentTemplate($arParams["tempList"]);
?>