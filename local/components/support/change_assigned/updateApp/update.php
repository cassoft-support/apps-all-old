<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] .$componentPath."/updateApp/updateObject.php";
require_once $_SERVER['DOCUMENT_ROOT'] .$componentPath."/updateApp/updateSetup.php";
//require_once $_SERVER['DOCUMENT_ROOT'] .$componentPath."/updateApp/updateMarketing.php";
require_once $_SERVER['DOCUMENT_ROOT'] .$componentPath."/updateApp/release.php";
$file_log = $_SERVER["DOCUMENT_ROOT"] .$componentPath."/updateApp/logUpdate.txt";

file_put_contents($file_log, print_r("update\n",true));
file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
$member = $_POST['authParams']['member_id'];
?>


<?
foreach($v1 as $app){

  $resApp = "update_".$app($member, $app);
  file_put_contents($file_log, print_r($resApp."\n",true), FILE_APPEND);
}