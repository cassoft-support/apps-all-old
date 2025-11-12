<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
$date = date("d.m.YTH:i:s");
$file_log = __DIR__ . "/logHuk.txt";

file_put_contents($file_log, print_r($date . "-huk\n", true));
//file_put_contents($file_log, print_r($_SERVER['DOCUMENT_ROOT'], true), FILE_APPEND);
//file_put_contents($file_log, print_r("REQUEST\n", true), FILE_APPEND);
//file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);

$arResult = $_REQUEST;

if ($arResult['app']) {
    $CloudApp = $arResult['app'];
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \Cassoft\Services\HlService($appAccess);
    $clientsApp = $HlClientApp->hl::getList([
        'select' => ['*'],
        'order' => ['ID' => 'ASC'],
        'filter' => [
            'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $arResult['member_id'],
            //  'UF_CS_CLIENT_PORTAL_APPLICATION_TOKEN' => $application_token,
        ]
    ])->fetchAll();

    if (empty($clientsApp)) {
        $folder = crypt($arResult['member_id'], 'CASsoft');
        $params = [
            'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $arResult['member_id'],
            'UF_CS_CLIENT_PORTAL_DOMEN' => $arResult['DOMAIN'],
            'UF_CS_CLIENT_PORTAL_DOMAIN' => $arResult['DOMAIN'],
            'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $arResult['AUTH_ID'],
            'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $arResult['REFRESH_ID'],
            'UF_DATE_UP' => date("d.m.YTH:i:s"),
            'UF_ACTIVE' => 1,
            'UF_CS_FOLDER' => $folder
        ];
       // file_put_contents($file_log, print_r($params, true), FILE_APPEND);
    }
    $authAppAdd = $HlClientApp->hl::add($params);
   // file_put_contents($file_log, print_r((array)$authAppAdd, true), FILE_APPEND);
}