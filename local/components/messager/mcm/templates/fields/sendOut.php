<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendOut.txt";
p($arParams, "start", $log);
if ($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
//    $params = [
//        'ENTITY' => 'setup_messager',
//        'sort' => [],
//        'filter' => [],
//    ];
//    $resSetup = $auth->CScore->call('entity.item.get', $params)[0]['PROPERTY_VALUES'];
//    $mcmConnector = json_decode($resSetup['CS_MCM_CONNECT'], true);
//    foreach ($mcmConnector as $key =>$value){
//        p($value , "con", $log);
//        p($key , "key", $log);
//
//        if($value['CONNECTOR'] ===  $arParams['data']['CONNECTOR'] && $value['LINE'] ==  $arParams['data']['LINE']){
//            p($value , "con", $log);
//            p($key , "key", $log);
//            $profile = $key;
//        }
//    }
    $profileLine =  profileLineCsMcm($arParams['auth']['member_id'], $arParams['data']['LINE'], $arParams['data']['CONNECTOR']);
   p($profileLine , "profileLine", $log);
    $profile = $profileLine[0]['UF_PROFILE_ID'];
p($profile , "profile", $log);
if(!empty($arParams['data']['MESSAGES'][0]['message']['text'])) {
    $dataMes = [
        "recipient" => $arParams['data']['MESSAGES'][0]['chat']['id'],
        "body" => preg_replace('/\[[^\]]*\]/', '', $arParams['data']['MESSAGES'][0]['message']['text']),
        // "recipient"=> $arParams['data']['MESSAGES'][0]['message']['files'][0]['name'],
        // "caption"=> $arParams['data']['MESSAGES'][0]['message']['files'][0]['name'],
        // "b64_file" =>  base64_encode($arParams['data']['MESSAGES'][0]['message']['files'][0]['name']),

    ];
    $urlMethod = '/api/sync/message/send?profile_id='.$profile;
}
    if(!empty($arParams['data']['MESSAGES'][0]['message']['files'])
       // && $arParams['data']['MESSAGES'][0]['message']['files'][0]['type'] === 'image'
    ){

        $dataMes["recipient"] = $arParams['data']['MESSAGES'][0]['chat']['id'];
        $dataMes["caption"] = $arParams['data']['MESSAGES'][0]['message']['files'][0]['name'];
        $dataMes["url"] =  $arParams['data']['MESSAGES'][0]['message']['files'][0]['link'];
      //  $dataMes["b64_file"] =  base64_encode(file_get_contents($arParams['data']['MESSAGES'][0]['message']['files'][0]['link']));
       // $urlMethod = '/api/sync/message/img/send?profile_id='.$profile;
        $urlMethod = '/api/async/message/file/url/send?profile_id='.$profile;
       // $dataMes["b64_file"] =  base64_encode(file_get_contents($arParams['data']['MESSAGES'][0]['message']['files'][0]['link']));
    }
    p($urlMethod , "urlMethod", $log);
    p($dataMes , "dataMes", $log);
    $result=  sendPostWappi($urlMethod, $dataMes);
    p($result , "result", $log);
//    $HlApp = new \CSlibs\B24\HL\HlService('app_auth_params');
//    $tokenWappi = $HlApp->getByFilterList(['UF_APP_NAME'=>'mcm'])[0]['UF_TOKEN_WAPPI'];
//    $tokenWappi = tokenWappi();
//p(json_encode($dataMes) , "dataMes", $log);
//    $curl = curl_init();
//    curl_setopt_array($curl, array(
//        CURLOPT_URL => 'https://wappi.pro'.$urlMethod.$profile,
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_ENCODING => '',
//        CURLOPT_MAXREDIRS => 10,
//        CURLOPT_TIMEOUT => 0,
//        CURLOPT_FOLLOWLOCATION => true,
//        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//        CURLOPT_CUSTOMREQUEST => 'POST',
//       CURLOPT_POSTFIELDS => json_encode($dataMes),
//        CURLOPT_HTTPHEADER => array(
//            'Authorization: '. $tokenWappi
//        ),
//    ));
//
//    $response = curl_exec($curl);
//
//    curl_close($curl);
//    echo $response;
//    $result = json_decode($response, true);
    p($result , "result", $log);
    if($result['message_id']){
//        $params=[
//            'CONNECTOR' => $arParams['data']['CONNECTOR'],
//            'LINE' => $arParams['data']['LINE'],
//            'MESSAGES' => [
//                [
//                    'im' => $result['uuid'],
//                    'message' => [
//                        'id' => [$result['message_id']]
//                    ],
//                    'chat' => [
//                        'id' => $arParams['data']['MESSAGES'][0]['chat']['id']
//                    ],
//                ],
//            ]
//        ];
//p($params , "params", $log);
        $resultDelivery = $auth->CScore->call('imconnector.send.status.delivery',$arParams['data']);
        p($resultDelivery , "resultDelivery", $log);
    }


//
//     => 996739959b061dc1beea53906e80e4cfcfbf93dffa6f234127ddf0cd77a1393c
//        )

}