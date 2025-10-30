<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendOut.txt";
p($arParams, "start", $log);
//p($arParams, date('c'), $log);
if ($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
    $paramsGet = [
        'ENTITY' => 'setup_messager',
        'FILTER'=>[
            'PROPERTY_CS_CONNECTOR'=> $arParams['data']['CONNECTOR'],
            'PROPERTY_CS_LINE'=> $arParams['data']['LINE'],
        ]
    ];
    p($paramsGet , "paramsGet", $log);
    $resSetupGet = $auth->CScore->call('entity.item.get', $paramsGet)[0]['PROPERTY_VALUES'];
    if(!empty($resSetupGet)){
        $profile = $resSetupGet['CS_PROFILE_ID'];
        if ($resSetupGet['CS_RESOURCE'] === 'wappi' && ($resSetupGet['CS_TYPE'] === 'Whatsapp' || $resSetupGet['CS_TYPE'] === 'whatsapp')) {
            $typeUrl = '/api';

        } else {
            //  [] => Telegram
            $typeUrl = '/tapi';
        }
    }else {

        $profileLine = profileLineCsMcm($arParams['auth']['member_id'], $arParams['data']['LINE'], $arParams['data']['CONNECTOR']);
        p($profileLine, "profileLine", $log);

        $profile = $profileLine[0]['UF_PROFILE_ID'];
        if ($profileLine[0]['UF_RESOURCE'] === 'wappi' && ($profileLine[0]['UF_TYPE'] === 'Whatsapp' || $profileLine[0]['UF_TYPE'] === 'whatsapp')) {
            $typeUrl = '/api';

        } else {
            //  [] => Telegram
            $typeUrl = '/tapi';
        }
    }
p($profile , "profile", $log);
if(!empty($arParams['data']['MESSAGES'][0]['message']['text'])) {
    $dataMes = [
        "recipient" => $arParams['data']['MESSAGES'][0]['chat']['id'],
        "body" => preg_replace('/\[[^\]]*\]/', '', $arParams['data']['MESSAGES'][0]['message']['text']),

    ];
    $urlMethod = $typeUrl.'/sync/message/send?bot_id=b24&profile_id='.$profile;
}
    if(!empty($arParams['data']['MESSAGES'][0]['message']['files'])
       // && $arParams['data']['MESSAGES'][0]['message']['files'][0]['type'] === 'image'
    ){

        $dataMes["recipient"] = $arParams['data']['MESSAGES'][0]['chat']['id'];
        if(!empty($arParams['data']['MESSAGES'][0]['message']['text'])){
            $caption = $text = str_replace(["[b]", "[/b]", "[br]"], ' ', $arParams['data']['MESSAGES'][0]['message']['text']);
            $dataMes["caption"] = $caption;
        }
//        else{ // название файла
//            $dataMes["caption"] = $arParams['data']['MESSAGES'][0]['message']['files'][0]['name'];
//        }

        $dataMes["url"] =  $arParams['data']['MESSAGES'][0]['message']['files'][0]['link'];
        $urlMethod = $typeUrl.'/sync/message/file/url/send?bot_id=b24&profile_id='.$profile;
    }
    p($urlMethod , "urlMethod", $log);
    p($dataMes , "dataMes", $log);
    $result=  sendPostWappi($urlMethod, $dataMes);
    p($result , "result", $log);
    
    if($result['status'] ==='done'){

        $resultDelivery = $auth->CScore->call('imconnector.send.status.delivery',$arParams['data']);
        p($resultDelivery , "resultDelivery", $log);
    }


}