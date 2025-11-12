<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendIn.txt";
p($arParams, 'start', $log);
if($arParams['app'] && $arParams['member_id']){
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);

if($arParams['message']['chats'][0]['messages'][0]['direction'] === 'in') {
$resObject = $arParams['message']['offers'][0]['externalId'];
  //  explode('_', $resObject);
 //   im.chat.get` или `im.dialog.get`


    $mess = $arParams['message']['chats'][0]['messages'][0]['content']['text'];
//    обращение по объекту
//" . $arParams['message']['offers'][0]['title'] . ",
//адрес " . $arParams['message']['offers'][0]['address'] . "
//Сделка №" . $arParams['message']['offers'][0]['externalId'] . "
//[URL=" . $arParams['message']['offers'][0]['url'] . "]ссылка на объект[/URL]";
//    "ATTACH" => Array(
//        "ID" => 1,
//        "COLOR_TOKEN" => "primary"
//            "COLOR" => "#29619b",
//            "BLOCKS" => Array(
//        Array(
//            "MESSAGE" => "API будет доступно в обновлении [B]im 24.0.0[/B]"
//        )
//    )
//    "IMAGE" => Array(
//        Array(
//            "NAME" => "Это Mantis",
//            "LINK" => "https://files.shelenkov.com/bitrix/images/mantis.jpg",
//            "PREVIEW" => "https://files.shelenkov.com/bitrix/images/mantis.jpg",
//            "WIDTH" => "1000",
//            "HEIGHT" => "638"
//        )
//    )

    $arMessage = [
//Массив описания пользователя
        'user' => array(
            'id' => $arParams['message']['users'][0]['userId'],//ID пользователя во внешней системе *
// 'last_name'=> $arParams['message']['users'][0][''],//Фамилия
            'name' => $arParams['message']['users'][0]['name'],//Имя
//            'picture' =>
//                array(
//                    'url' => $arParams['message']['users'][0]['avatar']['images']['small'],//Ссылка на аватарку пользователя, доступную для портала
//                ),
//  'url'=> $arParams['message']['users'][0][''],//Ссылка на профиль пользователя
//  'sex'=> $arParams['message']['users'][0][''],//Пол. Допустимо male и female
//  'email'=> $arParams['message']['users'][0][''], //email
// 'phone'=> $arParams['message']['users'][0][''], //телефон
            'skip_phone_validate' => 'Y', //В значении 'Y' позволяет не применять валидацию
//номера телефона пользователя. По умолчанию 'N'.
        ),
//Массив описания сообщения
        'message' => array(
            'id' => $arParams['message']['chats'][0]['messages'][0]['messageId'], //ID сообщения во внешней системе.*
            'date' => time(), //Время сообщения в формате timestamp *
            'disable_crm' => 'N',//отключить чат трекер (CRM трекер)
            'text' => htmlspecialchars($mess), //Текст сообщения. Должен быть указан элемент text или files.

        ),
        'files' => array(//Массив описаний файлов, где каждый файл описывается
            //массивом, со ссылкой, которая доступна порталу
           array('url' => $arParams['message']['offers'][0]['mainPhoto']['url'], 'name' => 'Объект'),

        ),
//Массив описания чата
        'chat' => array(
            'id' => $arParams['message']['chats'][0]['chatId'],//ID чата во внешней системе *
            'name' => $arParams['message']['chats'][0]['offerId'],//Имя чата во внешней системе
            'url' => $arParams['message']['offers'][0]['url']//Ссылка на чат во внешней системе
        ),

    ];
//$arMessage1 = [
//    'user' => [
//        'id' => 106,
//        'name' => htmlspecialchars('Николай'),
//    ],
//    'message' => [
//        'id' => false,
//        'date' => time(),
//        'text' => htmlspecialchars("кому повторно"),
//    ],
//    'chat' => [
//        'id' => $chatID,
//        'url' => htmlspecialchars('https://city.brokci.ru/cassoftApp/test/casChats/index.php'),
//    ],
//];
 p($arMessage , "arMessage", $log);
if(!empty($arParams['line'])){
    $lineId = $arParams['line'];
}else {
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
    $lineId = $resSetup['PROPERTY_VALUES']['CS_CIAN_LINE'];
}
p($lineId , "lineId", $log);
    $arMessageSend = $auth->CScore->call(
        'imconnector.send.messages',
        [
            'CONNECTOR' => "cs_cian_connector",
          //  'LINE' => $resSetup['PROPERTY_VALUES']['CS_CIAN_LINE'],
            'LINE' => $lineId,
            'MESSAGES' => [$arMessage],
           // 'USER_ID' =>
        ]
    );
}
p($arMessageSend , "arMessageSend", $log);
}

