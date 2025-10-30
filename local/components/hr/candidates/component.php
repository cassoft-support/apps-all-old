<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$date=date("d.m.YTH:i");
$file_log = __DIR__."/component.txt";
file_put_contents($file_log, print_r($date."\n",true));
file_put_contents($file_log, print_r(json_encode($_REQUEST),true), FILE_APPEND);
file_put_contents($file_log, print_r($arParams,true), FILE_APPEND);

$arResult['req']=$_REQUEST;
$arResult['app']=$arParams['app'];
$arResult['smartId']=$arParams['smartId'];
$arResult['candidateId']=$arParams['candidateId'];

file_put_contents($file_log, print_r($arResult,true), FILE_APPEND);
//d($arResult);
$this->IncludeComponentTemplate();