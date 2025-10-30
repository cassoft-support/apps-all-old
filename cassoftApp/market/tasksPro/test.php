<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_REQUEST, "start", $log);
$token='785026ea43c1bb0b1b842189cbca9197c05f424e';
$mcm = new \CSlibs\Api\MCM\mcmClass($token);
//pr($mcm, '');
$profileId='c35a4dc2-a49b&phone=79877955813';
//$qr = $mcm->curlGet('sync/qr/get', $profileId);
$qr = $mcm->curlGet('sync/auth/code', $profileId);
pr($qr, '');


?>
<!--<img src="--><?php //=$qr['qrCode']?><!--" alt="Base64 Image">-->
