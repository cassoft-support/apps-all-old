<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logIITestHandler.txt";
$logW = __DIR__."/logIITestHandlerWH.txt";
p("событие", date('c'), $log);
$message = file_get_contents("php://input");
p($message , "message", $log);
$webhookUrl = 'https://mcm-dev.cassoft.ru/api/apps/tgstat/in/b176cc28ddd836fa2c7d93e81fff15df&7824620140';
$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => $message,
    ],
];

$context = stream_context_create($options);
$response = file_get_contents($webhookUrl, false, $context);

// Логируем ответ от вебхука
p($response, "webhook_response", $logW);

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
//$memberId ='b176cc28ddd836fa2c7d93e81fff15df';
//$result =Array
//(
//    'id' => 904505213,
//    'username' => 'Rin9l',
//'first_name' => 'Rinat',
//'last_name' => 'Sibgatulin',
//'bot_started' => '05.06.2025 11:48',
//    'is_banned' => 'Нет',
//);
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

