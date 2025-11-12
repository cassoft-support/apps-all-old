<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$log = __DIR__ . "/logTest.txt";

pr('test', '');

    $keyBot = '5830465058:AAFxwYJYDZgf-m8uLTG_YWctGKAjhD3wRCM';
 $idGroupTg = '-4944463929';
$message='test';
            $resSendTo = mesSendGroupTg($message, $idGroupTg, $keyBot);
            $logI = __DIR__ . "/logIstambul.txt";
            p($resSendTo, "resSendDub", $log);
            pr($resSendTo, '');
