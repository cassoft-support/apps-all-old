<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logRobot.txt";
p($arParams, "start", $log);
//p($arParams, date('c'), $log);
if ($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
   $prop = $arParams['properties'];
   //находим профиль $prop['profile'][0]
    $paramsGet = [
        'ENTITY' => 'setup_messager',
        'FILTER'=>[
            'PROPERTY_CS_CODE'=> $prop['profile'][0],
        ]
    ];
p($paramsGet , "paramsGet", $log);
    $resSetupGet = $auth->CScore->call('entity.item.get', $paramsGet)[0]['PROPERTY_VALUES'];
    p($resSetupGet , "resSetupGet", $log);
    $currentTime = time();

// Сравниваем
    if ($resSetupGet['CS_DATE_CLOSE']>$currentTime && !empty($resSetupGet['CS_PROFILE_ID'])) {

        // проверяем номер $prop['number']
        $phones = explode(',', $prop['number']);
        $phones = array_filter($phones, function ($value) {
            return trim($value) !== '';
        });
        $cleanPhones = array_map(function ($phone) {
            return preg_replace('/[^0-9]/', '', trim($phone));
        }, $phones);
        $cleanPhones = array_unique($cleanPhones);
        $cleanPhones = array_values($cleanPhones);
        p($cleanPhones, "cleanPhones", $log);
        if(!empty($cleanPhones) ) {

            $urlGet = '/api/sync/contact/check?profile_id='.$resSetupGet['CS_PROFILE_ID'].'&phone='.$cleanPhones[0];
             $numberCheck = sendGetWappi($urlGet);
             p($numberCheck , "numberCheck", $log);
if($numberCheck['on_whatsapp'] == 1){

        //отправляем сообщение  $prop['text']

        $dataMes = [
            "recipient" => $cleanPhones[0],
            "body" => preg_replace('/\[[^\]]*\]/', '', $prop['text']),

        ];
        $urlMethod = '/api/sync/message/send?bot_id=b24&profile_id='.$resSetupGet['CS_PROFILE_ID'];

    p($urlMethod , "urlMethod", $log);
    p($dataMes , "dataMes", $log);
    $result=  sendPostWappi($urlMethod, $dataMes);
    p($result , "result", $log);
    }
        }
    }
}