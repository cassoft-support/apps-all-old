<?
define(NOT_CHECK_PERMISSIONS, true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/install/base/settings.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/classes/gf.php");
$date=date("d.m.YTH:i");
$file_log = __DIR__."/ajaxInstall.txt";
file_put_contents($file_log, print_r($date."\n",true));
//file_put_contents($file_log, print_r($_POST,true), FILE_APPEND);
$app=$_POST['app'];
//$reg=$_POST;
$CSRest = new  \CSRest($app);
foreach($arrayInstall[$app] as $keyFunction=>$valFunction){
  file_put_contents($file_log, print_r($valFunction ,true), FILE_APPEND);
  $resInstall=$keyFunction($app, $valFunction, $CSRest);
  file_put_contents($file_log, print_r($resInstall."\n" ,true), FILE_APPEND);
}
$resultInstall="yes";
echo $resultInstall;