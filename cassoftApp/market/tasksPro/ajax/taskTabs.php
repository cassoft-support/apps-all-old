<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$log = __DIR__ . "/logTaskTabs.txt";
p('start', 'start', $log);
p($_REQUEST, 'REQUEST', $log);
$arParams = $_REQUEST;
$option=json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $CloudApp = 'tasks_pro';
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp , "clientsApp", $log);
    if ($clientsApp["ID"] > 0) {
        $arParams = $_REQUEST;
        $arParams['app'] = $CloudApp;
        $arParams['taskId'] = $option['taskId'];

        $APPLICATION->IncludeComponent(
            "tasks_pro:tabs",
            'desctop',
            $arParams,
            false
        );
    }
}