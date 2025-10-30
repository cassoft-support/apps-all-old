<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
//$log = __DIR__ . "/logSendIn".$arParams['message']['messages'][0]['profile_id'].".txt";
$log = __DIR__ . "/logSendIn.txt";
$log1 = __DIR__ . "/logSendIn1.txt";
$logT = __DIR__ . "/logSendInTg.txt";
//$logApi = __DIR__ . "/logSendInApi".$arParams['message']['messages'][0]['profile_id'].".txt";
$logApi = __DIR__ . "/logSendInApi.txt";
p($arParams, 'start', $log);
//p($arParams, date('c'), $log);
if($arParams['app'] && $arParams['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);

    $messages = $arParams['message']['messages'][0];
    if($messages['profile_id'] === '92bfec8e-80b7'){
        p($arParams, date('c'), $log1);
    }
    if($messages['profile_id'] === '2d6b4542-9f18' || $messages['profile_id'] === 'ecd7b405-e7d4'){
        p($messages['chat_type'], 'start', $logT);
    }
    if ($messages['chat_type'] !== "chat" || $messages['chatId'] == 4602731069) {
        if($messages['profile_id'] === '2d6b4542-9f18' || $messages['profile_id'] === 'ecd7b405-e7d4'){
            p($messages['chat_type'], $messages['profile_id'], $logT);
        }
        if ($messages['contact_name']) {
            $userName = $messages['contact_name'];
        } else {
            $userName = preg_replace('/[^0-9]/', '', $messages['chatId']);
        }
        if ($messages['wh_type'] === 'incoming_message' || $messages['wh_type'] === 'outgoing_message_api' || $messages['wh_type'] === 'outgoing_message_phone') {
            $profile = $messages['profile_id'];
            if ($messages['reply_message']['body']) {
                $mess = "--------------------\n" . $messages['reply_message']['body'] . "\n------------------------\n";
            }
            $mess = $mess . $messages['body'];
            if ($messages['chat_type'] === 'dialog') {
                $phoneUser = preg_replace('/[^0-9]/', '', $messages['chatId']);
            }
            if (!empty($messages['contact_phone'])) {
                $phoneUser = preg_replace('/[^0-9]/', '', $messages['contact_phone']);
            }


            //   [] => 79877955469@c.us
            $arMessage = ['message' => array(
                'id' => $messages['id'], //ID сообщения во внешней системе.*
                'date' => time(), //Время сообщения в формате timestamp *
                'disable_crm' => 'N',//отключить чат трекер (CRM трекер)
                //  'text' => htmlspecialchars($mess), //Текст сообщения. Должен быть указан элемент text или files.

            ),
//Массив описания чата
                'chat' => array(
                    'id' => $messages['chatId'],//ID чата во внешней системе *
                    'name' => $userName,//Имя чата во внешней системе
                    // 'url' => $messages['url']//Ссылка на чат во внешней системе
                ),
            ];
            if ($messages['type'] === 'document' || $messages['type'] === 'image' || $messages['type'] === 'ptt' || $messages['type'] === 'audio' || $messages['type'] === 'video') {
                p($messages['type'], "type", $log);

                //  $arMessage['files'][] = ['url' => $messages['file_link'], 'name' =>$messages['file_name']];
                $arMessage['message']['files'][] = ['url' => $messages['file_link'], 'name' => $messages['file_name']];
                $arMessage['message']['text'] = $messages['caption'];
            }
            if ($messages['wh_type'] === 'incoming_message') {

                $arMessage['user'] = [
                    'id' => $messages['chatId'],//ID пользователя во внешней системе *
                    'name' => $userName,//Имя
                    //   'last_name',//Фамилия
//            'name',//Имя
//            'picture' => array(
//                'url'//Ссылка на аватарку пользователя, доступную для портала
//            ),
//            'url',//Ссылка на профиль пользователя
//            'sex',//Пол. Допустимо male и female
//            'email', //email
                    'phone' => $phoneUser, //телефон
                    'picture' =>
                        array(
                            'url' => $messages['thumbnail'],//Ссылка на аватарку пользователя, доступную для портала
                        ),
                    'skip_phone_validate' => 'N', //В значении 'Y' позволяет не применять валидацию
//номера телефона пользователя. По умолчанию 'N'.
                ];

                if ($messages['type'] === 'chat' || $messages['type'] === 'text') {
                    if ($messages['chat_type'] === 'group'){

                        $arMessage['message']['text'] = htmlspecialchars("* Сообщение от " . $messages['senderName'] . " * \n" .$mess);
                    }else{
                        $arMessage['message']['text'] = htmlspecialchars($mess);
                    }

                }


            }
            if (($messages['wh_type'] === 'outgoing_message_api' || $messages['wh_type'] === 'outgoing_message_phone') && $messages['wappi_bot_id'] !== 'b24') {
                p($messages, "start", $logApi);
                if (!empty($messages['wappi_bot_id'])) {
                    $name = 'ИИ';
                    $arMessage['user'] = [
                        'id' => $messages['to'],//ID пользователя во внешней системе *
                        'name' => $name,//Имя
                        'phone' => $phoneUser, //телефон
                        'skip_phone_validate' => 'N',

                    ];
                    $paramsMes = [
                        //   'BOT_ID' => $botId,
                        'DIALOG_ID' => 11,
                        'MESSAGE' => $mess,
                        'ATTACH' => [
                            "ID" => 1,
                            "COLOR_TOKEN" => "primary",
                            "COLOR" => "#d70b0b",
                        ],
                        'KEYBOARD' => '',
                        'MENU' => '',
                        'SYSTEM' => 'Y',
                        'URL_PREVIEW' => 'Y'
                    ];
                    p($paramsMes, "paramsMesBot", $logApi);
                    //  $messBot = $auth->CScore->call('imbot.message.add', $paramsMes);
//p($messBot , "messBot", $logApi);
                } else {
                    $name = $messages['senderName'];
                    //  $arMessage['message']['user_id'] = 1;
                    $arMessage['user'] = [
                        'id' => $messages['to'],//ID пользователя во внешней системе *
                        'name' => $userName,//Имя
                        'phone' => $phoneUser, //телефон
                        'skip_phone_validate' => 'N',
                    ];
                }


                if ($messages['type'] === 'chat' || $messages['type'] === 'text') {
                    $arMessage['message']['text'] = htmlspecialchars("* Сообщение от " . $name . " * \n" . $messages['body']);
                }
            }
            p($messages['type'], "type1", $log);

            // $arMessage['message']['user_id'] = 1;
            p($arMessage, "arMessage", $log);
            p($_POST['member_id'], $profile, $log);
            $mcmConnector = profileIdCsMcm($arParams['member_id'], $profile);;
            $paramsGet = [
                'ENTITY' => 'setup_messager',
                'FILTER'=>[
                    'PROPERTY_CS_PROFILE_ID'=> $profile,
                ]
            ];
            p($paramsGet , "paramsGet", $log);
            $resSetupGet = $auth->CScore->call('entity.item.get', $paramsGet)[0]['PROPERTY_VALUES'];
            if($messages['profile_id'] === '2d6b4542-9f18' || $messages['profile_id'] === 'ecd7b405-e7d4'){
                p($resSetupGet, "resSetupGet", $logT);
            }
            p($mcmConnector, "mcmConnector", $log);
            if(!empty($resSetupGet)){
                $paramsIm = [
                    'CONNECTOR' => $resSetupGet['CS_CONNECTOR'],
                    'LINE' => $resSetupGet['CS_LINE'],
                    'MESSAGES' => [$arMessage],
                ];
            }else{
                $paramsIm = [
                    'CONNECTOR' => $mcmConnector['UF_CONNECTOR'],
                    'LINE' => $mcmConnector['UF_LINE_B24'],
                    'MESSAGES' => [$arMessage],
                ];
            }

            if($messages['profile_id'] === '2d6b4542-9f18' || $messages['profile_id'] === 'ecd7b405-e7d4'){
                p($paramsIm, "paramsIm", $logT);
            }
            p($paramsIm, "paramsIm", $log);
            $arMessageSend = $auth->CScore->call('imconnector.send.messages', $paramsIm);
            if($messages['profile_id'] === '92bfec8e-80b7'){
                p($arMessageSend, 'arMessageSend', $log1);
            }
            if($messages['profile_id'] === '2d6b4542-9f18' || $messages['profile_id'] === 'ecd7b405-e7d4'){
                p($arMessageSend, "arMessageSend", $logT);
            }
            p($arMessageSend, "arMessageSend", $log);
        }
    }
}

