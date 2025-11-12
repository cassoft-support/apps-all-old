<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logHandlerHH.txt";
p($_GET, "start", $log);


// Проверьте, что параметр 'state' присутствует и совпадает с сохраненным значением
if (isset($_GET['state']) && isset($_GET['code']) ) {
    $resState = explode("|", $_GET['state']);
    $memberId = $resState[0];
    if ($memberId) {
        $CloudApp = "hr_pro";
        $appAccess = 'app_' . $CloudApp . '_access';
        $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
        $clientsApp = $HlClientApp->searchID($memberId);
        p($clientsApp, "rest", $log);
        if ($clientsApp["ID"] > 0) {
                $arParams['code'] = $_GET['code'];
                $arParams['user_id'] = $resState[1];
                $arParams['auth']['member_id']  = $memberId;
                $arParams['app'] = $CloudApp;
                $arParams['tempList'] = 'codeAdd';
                $APPLICATION->IncludeComponent(
                    "settings:base",
                    'hr_pro',
                    $arParams,
                    false
                );

        }
    }

}





