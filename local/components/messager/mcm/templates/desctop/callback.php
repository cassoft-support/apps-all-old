<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logCallBack.txt";
p($arParams, "start", $log);

$secretKey = '2C71BACA7FB4C8053768FCF07542E36A';

$isValid = verifySignature($arParams['base'], $secretKey);
p($isValid, "isValid", $log);
p($isValid ? 'Подпись валидна' : 'Подпись НЕ валидна', $log);