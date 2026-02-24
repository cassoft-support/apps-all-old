<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendIn.txt";
p($arParams, 'start', $log);
if($arParams['app'] && $arParams['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);

    if (!empty($arParams['message']['messages'])) {
//$resObject = $arParams['message']['offers'][0]['externalId'];
        //  explode('_', $resObject);
        //   im.chat.get` или `im.dialog.get`
        foreach ($arParams['message']['users'] as $user) {
            $arUser[$user['user_id']] = $user['roles'][0];
        }
        p($arUser , "arUser", $log);
        p($arUser[$arParams['message']['messages'][0]['user_id']] , "arUser", $log);
        if ($arUser[$arParams['message']['messages'][0]['user_id']] === 'BUYER') {
            $mess = $arParams['message']['messages'][0]['message'];
            
            // Находим отправителя сообщения (BUYER) в массиве пользователей
            $senderId = $arParams['message']['messages'][0]['user_id'];
            $sender = null;
            foreach ($arParams['message']['users'] as $user) {
                if ($user['user_id'] == $senderId) {
                    $sender = $user;
                    break;
                }
            }
            p($sender, "sender (BUYER)", $log);
            
            // Разделяем display_name на имя и фамилию
            $nameParts = explode(' ', trim($sender['display_name']), 2);
            $firstName = $nameParts[0]; // Имя (первая часть)
            $lastName = isset($nameParts[1]) ? $nameParts[1] : ''; // Фамилия (вторая часть)
            
            $arMessage = [
//Массив описания пользователя
                'user' => array(
                    'id' => $sender['user_id'],//ID пользователя во внешней системе *
                    'last_name' => $lastName,//Фамилия
                    'name' => $firstName,//Имя
                    'picture' =>
                        array(//      'url' => $sender['avatar']['images']['small'],//Ссылка на аватарку пользователя, доступную для портала
                        ),
//  'url'=> $arParams['message']['users'][0][''],//Ссылка на профиль пользователя
//  'sex'=> $arParams['message']['users'][0][''],//Пол. Допустимо male и female
//  'email'=> $arParams['message']['users'][0][''], //email
// 'phone'=> $arParams['message']['users'][0][''], //телефон
                    'skip_phone_validate' => 'Y', //В значении 'Y' позволяет не применять валидацию
//номера телефона пользователя. По умолчанию 'N'.
                ),
//Массив описания сообщения
                'message' => array(
                    'id' => $arParams['message']['messages'][0]['id'], //ID сообщения во внешней системе.*
                    'date' => time(), //Время сообщения в формате timestamp *
                    'disable_crm' => 'N',//отключить чат трекер (CRM трекер)
                    'text' => htmlspecialchars($mess), //Текст сообщения. Должен быть указан элемент text или files.

                ),
                'files' => array(//Массив описаний файлов, где каждый файл описывается
                    //массивом, со ссылкой, которая доступна порталу
                    //  array('url' => $arParams['message']['result']['webhook_message']['url'], 'name' => 'Объект'),

                ),
//Массив описания чата
                'chat' => array(
                    'id' => $arParams['message']['messages'][0]['chat_id'],//ID чата во внешней системе *
                    // 'name' => $arParams['message']['chats'][0]['offerId'],//Имя чата во внешней системе
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
            p($arMessage, "arMessage", $log);

            $params = [
                'ENTITY' => 'setup_messager',
                'sort' => [],
                'filter' => [],
            ];
            $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
            p($resSetup['PROPERTY_VALUES'], "setup", $log);
            $paramsMessage = [
                'CONNECTOR' => "cs_domclick_connector",
                'LINE' => $resSetup['PROPERTY_VALUES']['CS_DC_LINE'],
                'MESSAGES' => [$arMessage],
                // 'USER_ID' =>
            ];
            $arMessageSend = $auth->CScore->call('imconnector.send.messages', $paramsMessage);
            p($arMessageSend, "arMessageSend", $log);
        }
    }
}

