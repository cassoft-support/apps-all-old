<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logIITestHandlerBotoca.txt";
p("событие", date('c'), $log);
$message = file_get_contents("php://input");
p($message , "message", $log);

if(!empty($message)){
    $resDecode = json_decode($message, true);
    if(!empty($resDecode)){
        $result=$resDecode;
    }else{
        parse_str($message, $result);
    }

p($result, "result", $log);
$fileInfo = pathinfo(basename(__FILE__))["filename"];
$resName = explode("_", $fileInfo);
$memberId = $resName[0];
$ProfileId = $resName[1];

p($memberId, "memberId", $log);
        if ($memberId) {
        $CloudApp = "mcm";
        $appAccess = 'app_' . $CloudApp . '_access';
        $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
        $clientsApp = $HlClientApp->searchAppID($memberId, 'tgstat');
        p($clientsApp, "rest", $log);
                if ($clientsApp["ID"] > 0) {

                $arParams['result'] = $result;
                $arParams['app'] = $CloudApp;
                $arParams['member_id'] = $memberId;
                $arParams['app_code'] = 'tgstat';
                $APPLICATION->IncludeComponent(
                "event:tgstat",
                'desctop',
                $arParams,
                false
                );
                }
        }
}

header("Content-Type: application/json");
echo json_encode([
    'status' => 'success',
]);
exit;