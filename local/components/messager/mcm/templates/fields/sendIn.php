<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logSendIn.txt";
$logApi = __DIR__ . "/logSendInApi.txt";
p($arParams, date('c'), $log);
if($arParams['app'] && $arParams['member_id']){
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);

$messages = $arParams['message']['messages'][0];

if( $messages['wh_type'] === 'incoming_message' || $messages['wh_type'] === 'outgoing_message_api' || $messages['wh_type'] === 'outgoing_message_phone' ) {
$profile = $messages['profile_id'];
    $mess = $messages['body'];


    $arMessage = [ 'message' => array(
        'id' => $messages['id'], //ID сообщения во внешней системе.*
        'date' => time(), //Время сообщения в формате timestamp *
        'disable_crm' => 'N',//отключить чат трекер (CRM трекер)
        //  'text' => htmlspecialchars($mess), //Текст сообщения. Должен быть указан элемент text или files.

    ),
//Массив описания чата
            'chat' => array(
        'id' => $messages['chatId'],//ID чата во внешней системе *
        'name' => $messages['senderName'],//Имя чата во внешней системе
        // 'url' => $messages['url']//Ссылка на чат во внешней системе
    ),
        ];
    if($messages['type'] === 'document' || $messages['type'] === 'image'){
        p($messages['type'], "type", $log);

        //  $arMessage['files'][] = ['url' => $messages['file_link'], 'name' =>$messages['file_name']];
        $arMessage['message']['files'][] = ['url' => $messages['file_link'], 'name' =>$messages['file_name']];
        $arMessage['message']['text'] = $messages['caption'];
    }
    if( $messages['wh_type'] === 'incoming_message'  ) {

        $arMessage['user'] =[
                'id' => $messages['from'],//ID пользователя во внешней системе *
                'name' => $messages['senderName'],//Имя
                'picture' =>
                    array(
                        'url' => $messages['thumbnail'],//Ссылка на аватарку пользователя, доступную для портала
                    ),
                'skip_phone_validate' => 'Y', //В значении 'Y' позволяет не применять валидацию
//номера телефона пользователя. По умолчанию 'N'.
        ];
        if($messages['type'] === 'chat'){
            $arMessage['message']['text'] = htmlspecialchars($mess);
        }
    }
   if($messages['wh_type'] === 'outgoing_message_api' || $messages['wh_type'] === 'outgoing_message_phone'){
        p($messages , "messages", $logApi);
        if(!empty($messages['wappi_bot_id'])){
            $name ='MegaBrain';
            $arMessage['user'] =[
                'id' => $messages['to'],//ID пользователя во внешней системе *
                'name' => $name,//Имя
//                'picture' =>
//                    array(
//                        'url' => $messages['thumbnail'],//Ссылка на аватарку пользователя, доступную для портала
//                    ),
//                'skip_phone_validate' => 'Y', //В значении 'Y' позволяет не применять валидацию
//номера телефона пользователя. По умолчанию 'N'.
            ];
        }else{
            $name =$messages['senderName'];
          //  $arMessage['message']['user_id'] = 1;
            $arMessage['user'] =[
                'id' => $messages['to'],//ID пользователя во внешней системе *
                'name' => $name,//Имя
            ];
        }


       if($messages['type'] === 'chat'){
           $arMessage['message']['text'] = htmlspecialchars("==== Сообщение от ".$name." ==== \n".$mess);
       }
    }
    p($messages['type'], "type1", $log);

  // $arMessage['message']['user_id'] = 1;
 p($arMessage , "arMessage", $log);
    p($_POST['member_id'], $profile, $log);
   $mcmConnector = profileIdCsMcm($arParams['member_id'], $profile);;
p($mcmConnector , "mcmConnector", $log);
    $arMessageSend = $auth->CScore->call(
        'imconnector.send.messages',
        [
            'CONNECTOR' => $mcmConnector['UF_CONNECTOR'],
            'LINE' => $mcmConnector['UF_LINE_B24'],
            'MESSAGES' => [$arMessage],
        ]
    );
    p($arMessageSend , "arMessageSend", $log);
}
}

