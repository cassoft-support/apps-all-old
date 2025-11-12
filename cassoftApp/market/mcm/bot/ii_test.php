<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logIITestHandler.txt";
p("событие", date('c'), $log);
$message = file_get_contents("php://input");
p($message , "message", $log);
$headers = getallheaders();
p($headers , "headers", $log);
$result=json_decode($message, true);
p($result, "result", $log);