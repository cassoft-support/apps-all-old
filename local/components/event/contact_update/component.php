<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$date=date("d.m.YTH:i");
$file_log = __DIR__."/component.txt";
file_put_contents($file_log, print_r($date."\n",true));
file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
$arResult['req']=json_encode($_REQUEST);

$this->IncludeComponentTemplate();