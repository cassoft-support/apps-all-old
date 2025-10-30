<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__ . "/logHuk.txt";

p($_REQUEST, "start", $log);
p($_REQUEST, "REQUEST", $log);
$arResult = $_REQUEST;
if ($arResult['app'] && $arResult['member_id']) {
    $CloudApp = $arResult['app'];
    $appAccess = 'app_mcm_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($arResult['member_id']);
    p($clientsApp, "clientsApp", $log);
    if (empty($clientsApp["ID"])) {
        if(!empty($arResult['FOLDER'])){
            $folder = crypt($arResult['member_id'], 'CASsoft');
        }else{
            $folder = $arResult['FOLDER'];
        }

        $params = [
            'UF_CS_CLIENT_PORTAL_MEMBER_ID' => $arResult['member_id'],
            'UF_APP' => $arResult['app'],
            'UF_CS_CLIENT_PORTAL_DOMAIN' => $arResult['DOMAIN'],
            'UF_CS_CLIENT_PORTAL_ACCESS_TOKEN' => $arResult['AUTH_ID'],
            'UF_CS_CLIENT_PORTAL_REFRESH_TOKEN' => $arResult['REFRESH_ID'],
            'UF_DATE_UP' => date("d.m.YTH:i:s"),
            'UF_ACTIVE' => 1,
            'UF_CS_FOLDER' => $folder
        ];
        p($params, "params", $log);
        $authAppAdd = $HlClientApp->hl::add($params);
        p($authAppAdd, "authAppAdd", $log);
    }

}