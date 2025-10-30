<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$date=date("d.m.YTH:i");
$log = __DIR__."/component.txt";
p($_REQUEST , "start", $log);
p($arParams , "start", $log);


$this->IncludeComponentTemplate();