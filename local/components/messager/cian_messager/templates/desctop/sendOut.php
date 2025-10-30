<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendOut.txt";
p($arParams, date('d-m-Y-H-i-s'), $log);
if ($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [
            'PROPERTY_CS_CIAN_LINE'=> $arParams['data']['LINE']
        ],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0]['PROPERTY_VALUES'];

    $dataMes = [
        "chatId"=> $arParams['data']['MESSAGES'][0]['chat']['id'],
        "content"=>[
          //  "text" =>preg_replace('/\[[^\]]*\]/', '', str_replace('[br]', '\n',$arParams['data']['MESSAGES'][0]['message']['text']))
            "text" =>preg_replace('/\[[^\]]*\]/', '', $arParams['data']['MESSAGES'][0]['message']['text'])
        ]
    ];
p($dataMes , "dataMes", $log);
    $keyCian = "Authorization: Bearer ".$resSetup['CS_KEY_CIAN'];

    $ch = curl_init('https://public-api.cian.ru/v1/send-message');
    curl_setopt(  $ch,CURLOPT_HTTPHEADER,array($keyCian ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataMes, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    //$res = json_encode($res, JSON_UNESCAPED_UNICODE);
    $result = json_decode($res, true);
    p($result , "result", $log);
    if($result['result']['messageId']){
//        $params=[
//            'CONNECTOR' => $arParams['data']['CONNECTOR'],
//            'LINE' => $arParams['data']['LINE'],
//            'MESSAGES' => [
//                [
//                    'im' => [$result['result']['chatId']],
//                    'message' => [
//                        'id' => [$result['result']['messageId']]
//                    ],
//                    'chat' => [
//                        'id' => $arParams['data']['MESSAGES'][0]['chat']['id']
//                    ],
//                ],
//            ]
//        ];
//        p($params , "params", $log);
        $resultDelivery = $auth->CScore->call(
            'imconnector.send.status.delivery',$arParams['data']

        );
        p($resultDelivery , "resultDelivery", $log);
    }


//
//     => 996739959b061dc1beea53906e80e4cfcfbf93dffa6f234127ddf0cd77a1393c
//        )

}