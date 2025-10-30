<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendIn.txt";
p($arParams, date('c'), $log);
//                    [url] => https://auto.ru/cars/used/sale/1128470663-d95cb236/
//                    [vin] => WAUZZF2XPN122562D
//[address] => Красноярск Красноярск, Брянская улица, 56
//                    [coord] => 56.021004, 92.863174
//                    [title] => DVApro : Audi A6  , 3 910 000 руб

//
//            [message] => Array
//                        (
//                            [type] => text
//                            [text] => tets-2
//                                            [id] => f75e8671-e720-4b31-8d27-3e2afbcc2ac2
//                                        )
//
//                                )
//


if($arParams['app'] && $arParams['member_id'] && !empty($arParams['message']['message'])) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id'], $arParams['app_code']);
//p($auth , "auth", $log);
            $arMessage = [
//Массив описания пользователя
                'user' => array(
                    'id' => $arParams['message']['sender']['id'],//ID пользователя во внешней системе *
                    'name' => $arParams['message']['sender']['name'],//Имя
                    'picture' =>
                        array(      'url' => $arParams['message']['sender']['photo'],//Ссылка на аватарку пользователя, доступную для портала
                        ),
                    'skip_phone_validate' => 'Y', //В значении 'Y' позволяет не применять валидацию
//номера телефона пользователя. По умолчанию 'N'.
                ),
//Массив описания сообщения
                'message' => array(
                    'id' => $arParams['message']['message']['id'], //ID сообщения во внешней системе.*
                    'date' => time(), //Время сообщения в формате timestamp *
                    'disable_crm' => 'N',//отключить чат трекер (CRM трекер)
                    'text' => htmlspecialchars($arParams['message']['message']['text']), //Текст сообщения. Должен быть указан элемент text или files.

                ),
                'files' => array(//Массив описаний файлов, где каждый файл описывается
                    //массивом, со ссылкой, которая доступна порталу
                    //  array('url' => $arParams['message']['result']['webhook_message']['url'], 'name' => 'Объект'),

                ),
//Массив описания чата
                'chat' => array(
                    'id' => $arParams['message']['sender']['id'],//ID чата во внешней системе *
                    'name' => $arParams['message']['sender']['title'],//Имя чата во внешней системе
                    'url' => $arParams['message']['sender']['url']//Ссылка на чат во внешней системе
                ),

            ];

            p($arMessage, "arMessage", $log);

            $params = [
                'ENTITY' => 'setup_messager',
                'sort' => [],
                'filter' => [
                    'PROPERTY_CS_PROFILE_ID' => $arParams['profile']
                ],
            ];
            $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
           p($resSetup , "resSetup", $log);
           // p($resSetup['PROPERTY_VALUES'], "setup", $log);
//    $activate = $auth->CScore->call(
//        'imconnector.activate',
//        [
//            'CONNECTOR' => 'cs_auto_ru',
//            'LINE' => 15,
//            'ACTIVE' => true,
//        ]
//    );
    p($activate , "activate", $log);
            $paramsMessage = [
                'CONNECTOR' => "cs_auto_ru",
                'LINE' => $resSetup['PROPERTY_VALUES']['CS_LINE'],
                'MESSAGES' => [$arMessage],
                // 'USER_ID' =>
            ];
            p($paramsMessage , "paramsMessage", $log);
         $arMessageSend = $auth->CScore->call('imconnector.send.messages', $paramsMessage);
            p($arMessageSend, "arMessageSend", $log);
            $chatId = $arMessageSend['DATA']['RESULT'][0]['session']['CHAT_ID'];
    $chatRes = $auth->CScore->call('imopenlines.session.history.get',
          [
              'CHAT_ID'=> $chatId
          ]);
    p($chatRes, "chatRes", $log);
    if (array_contains_string($chatRes, "🟢Обращение по объявлению")) {
        p("Фраза найдена", $log);
    } else {
    $userCurrent = $auth->CScore->call('user.current');
  // p($userCurrent , "userCurrent", $log);
$entityRes = explode('|', $chatRes['chat'][$chatId]['entityData1']);
$entityType = strtolower($entityRes[1]);
$mesInfo = "🟢Обращение по объявлению \n";

    $blocks[]=  ["MESSAGE" => $arParams['message']['sender']['title']];
    $blocks[]=  ["IMAGE" => [
        "NAME" => 'фото авто',
        "LINK" => $arParams['message']['sender']['url'],
        "PREVIEW" => $arParams['message']['sender']['url'],
        "WIDTH" => 100,
        "HEIGHT" => 100,
    ]];

    $blocks[]=  ["DELIMITER" =>[ "SIZE" => 400, ]];
    $paramsMes = [
        'BOT_ID' => 2125,
        'DIALOG_ID' => 'chat'.$chatId,
        'MESSAGE' => $mesInfo,
        'ATTACH' => [
            "ID" => 1,
            "COLOR_TOKEN" => "primary",
            "COLOR" => "#d70b0b",
            "BLOCKS" => $blocks
        ],
        'KEYBOARD' => '',
        'MENU' => '',
        'SYSTEM' => 'Y',
        'URL_PREVIEW' => 'Y'
    ];
    p($paramsMes , "paramsMes", $log);
    $messBot = $auth->CScore->call('imbot.message.add', $paramsMes);
    p($messBot , "messBot", $log);


    }
//    $regBot = $auth->CScore->call('imbot.register',
//    Array(
//        'CODE' => 'cs_auto_bot',
//        'TYPE' => 'S',
//        'EVENT_HANDLER' => 'http://app.cassoft.ru/cassoftApp/market/autoRu/ajax/autoBot.php',
//        'OPENLINE' => 'Y',
//        'CLIENT_ID' => '',
//        'PROPERTIES' => Array(
//            'NAME' => 'Валера',
//            'LAST_NAME' => '',
//            'COLOR' => 'GREEN',
//            'EMAIL' => 'auto@cassoft.ru',
//            'PERSONAL_BIRTHDAY' => '2016-03-11',
//            'WORK_POSITION' => 'Лучший сотрудник',
//            'PERSONAL_WWW' => 'http://test.ru',
//            'PERSONAL_GENDER' => 'M',
//            'PERSONAL_PHOTO' => '',
//        )
//    ),);
//p($regBot, "start", $log);


}

function array_contains_string($array, $searchString) {
    foreach ($array as $item) {
        if (is_array($item)) {
            // Рекурсивно проверяем подмассив
            if (array_contains_string($item, $searchString)) {
                return true;
            }
        } else {
            // Проверяем, содержит ли элемент строку
            if (is_string($item) && strpos($item, $searchString) !== false) {
                return true;
            }
        }
    }
    return false;
}