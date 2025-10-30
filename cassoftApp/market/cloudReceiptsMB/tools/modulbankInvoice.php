<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/lib/bitrixAuth/CSrest.php');
$file_log = "/home/bitrix/www/pub/cassoftApp/ModulbankPay/logInvoce.txt";

file_put_contents($file_log, print_r("create-complex",true));
  file_put_contents($file_log, print_r($_REQUEST,true), FILE_APPEND);
$CSRest = new CSRest("testirovanie_metodov");
echo "Выводим результат!";
function d($print){echo "<pre>"; print_r($print); echo "</pre>";}