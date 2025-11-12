<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendOut.txt";
p($arParams, 'start', $log);
if ($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id'], $arParams['app_code']);

    $params = [
        'ENTITY' => 'setup_messager',
        'filter' => [
            'PROPERTY_CS_LINE'=> $arParams['data']['LINE'],
          'PROPERTY_CS_CONNECTOR'=> $arParams['data']['CONNECTOR']
        ],
    ];

p($params , "params", $log);
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0]['PROPERTY_VALUES'];
    p($resSetup , "resSetup", $log);
    $chatId= $arParams['data']['MESSAGES'][0]['chat']['id'];
    if(!empty($arParams['data']['MESSAGES'][0]['message']['files'])){
        $files= $arParams['data']['MESSAGES'][0]['message']['files'][0]['link'];
    }
    if(!empty($arParams['data']['MESSAGES'][0]['message']['user_id'])){
        $user = $auth->CScore->call("user.get",['ID'=>$arParams['data']['MESSAGES'][0]['message']['user_id']] )[0];
        p($user , "user", $log);
    }
    if(!empty($arParams['data']['MESSAGES'][0]['message']['text'])){
        $type='text';
        $text= preg_replace('/\[[^\]]*\]/', '', $arParams['data']['MESSAGES'][0]['message']['text'])."\n";
    }

  //  $profile = $resSetup['CS_PROFILE_ID'];
  $profile = 'krs0613-61a1a55d6648382551cef2b7935abb40c91f2ac2';
  $profileChat = 'DVApro123';
 //  $profile = '61a1a55d6648382551cef2b7935abb40c91f2ac2';
    $dataMes = [

"sender"=> [
    "name"=> $user['NAME'],
    //    "photo": {string},
],
  "recipient"=> [
     //   "id"=> $chatId
      "id"=> $chatId
  ],
  "message" => [
        "type"=> $type,
    "id"=> $arParams['data']['MESSAGES'][0]['im']['message_id'],
    "text"=> $text,
    "file"=> $files
    ]
    ];
p($dataMes , "dataMes", $log);

    $url = "https://chat-api-ext.vertis.yandex.net/api/1.0/aggregators/auto/hook?token=" . $profileChat;
p($url , "url", $log);
// Инициализация cURL
    $ch = curl_init();

// Настройка запроса
    $ch =curl_init('https://chat-api-ext.vertis.yandex.net/api/1.x/aggregators/auto/hook?token='. $profileChat);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataMes, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer '. $profile
    ]);

// Выполнение запроса
    $response = curl_exec($ch);
p($response , "response", $log);
    $result = json_decode($response, true);
    p($result , "result", $log);
 if(empty($result)){
     p($result , "result2", $log);
        $resultDelivery = $auth->CScore->call(
            'imconnector.send.status.delivery', $arParams['data']
//            [
//                'CONNECTOR' => $arParams['data']['CONNECTOR'],
//                'LINE' => $arParams['data']['LINE'],
//                'MESSAGES' => [
//                    [
//                        'im' => [$result['result']['chatId']],
//                        'message' => [
//                            'id' => [$result['result']['messageId']]
//                        ],
//                        'chat' => [
//                            'id' => $arParams['data']['MESSAGES'][0]['chat']['id']
//                        ],
//                    ],
//                ]
//            ]
        );
        p($resultDelivery , "resultDelivery", $log);
    }


//
//     => 996739959b061dc1beea53906e80e4cfcfbf93dffa6f234127ddf0cd77a1393c
//        )

}