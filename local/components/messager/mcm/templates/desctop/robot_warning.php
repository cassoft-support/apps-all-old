<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logRobotWarning.txt";
p($arParams, "start", $log);
//p($arParams, date('c'), $log);
if ($arParams['app'] && $arParams['auth']['member_id']) {
  //  $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
   $resSend=   mesSendGroupTg($arParams['properties']['text'], $arParams['properties']['group_tg'], $arParams['properties']['bot_key']);
   p($resSend , "resSend", $log);
}