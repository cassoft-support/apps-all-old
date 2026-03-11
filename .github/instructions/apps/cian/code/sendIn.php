<?php
/**
 * CIAN Messenger - Обработка входящих сообщений
 * 
 * Файл: local/components/messager/cian_messager/templates/desctop/sendIn.php
 * Назначение: Прием webhooks от CIAN и отправка в Bitrix24 Open Lines
 * 
 * Особенности:
 * - Обработка сообщений от CIAN бота (userId: 68084393)
 * - Запрос к API get-chat для определения реального покупателя
 * - Разделение имени на имя и фамилию
 * - Передача фото объекта как вложения
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logSendIn.txt";
p($arParams, "дата - ".date('c')."\n", $log);

if($arParams['app'] && $arParams['member_id']){
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);

    if($arParams['message']['chats'][0]['messages'][0]['direction'] === 'in') {
        
        $mess = $arParams['message']['chats'][0]['messages'][0]['content']['text'];
        
        // Получаем ID отправителя сообщения
        $senderUserId = $arParams['message']['chats'][0]['messages'][0]['userId'];
        $chatId = $arParams['message']['chats'][0]['chatId'];
        
        // ============================================================
        // ОБРАБОТКА СООБЩЕНИЙ ОТ CIAN БОТА
        // ============================================================
        // CIAN отправляет автоматические сообщения от бота (ID: 68084393)
        // Проблема: все покупатели создавались как один контакт
        // Решение: запрос к get-chat API для определения реального покупателя
        
        if ($senderUserId == 68084393) {
            p("Обнаружено сообщение от ЦИАН бота, запрашиваем данные чата", "cian_bot_detected", $log);
            
            // Получаем API ключ из настроек
            $params = [
                'ENTITY' => 'setup_messager',
                'sort' => [],
                'filter' => [],
            ];
            $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
            $apiKey = $resSetup['PROPERTY_VALUES']['CS_KEY_CIAN'];
            
            // Запрос к API ЦИАН для получения информации о чате
            $url = "https://public-api.cian.ru/v1/get-chat?chatId={$chatId}";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$apiKey}"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            p("API get-chat HTTP Code: {$httpCode}", "api_response", $log);
            
            if ($httpCode == 200) {
                $chatData = json_decode($response, true);
                p($chatData, "chatData", $log);
                
                // Ищем initiator (покупателя) в массиве users
                $buyerUserId = null;
                $buyerName = null;
                if (!empty($chatData['result']['chat']['users'])) {
                    foreach ($chatData['result']['chat']['users'] as $user) {
                        if ($user['role'] === 'initiator') {
                            $buyerUserId = $user['userId'];
                            $buyerName = isset($user['name']) ? $user['name'] : null;
                            p("Найден покупатель (initiator): {$buyerUserId}, имя: {$buyerName}", "buyer_found", $log);
                            break;
                        }
                    }
                }
                
                // Если нашли покупателя, используем его данные
                if ($buyerUserId) {
                    $senderUserId = $buyerUserId;
                    if ($buyerName) {
                        $senderName = $buyerName;
                        // Разделяем имя на имя и фамилию
                        $nameParts = explode(' ', trim($buyerName), 2);
                        $firstName = $nameParts[0];
                        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                    } else {
                        $senderName = "Покупатель";
                        $firstName = "Покупатель";
                        $lastName = $buyerUserId;
                    }
                } else {
                    // Если не нашли, используем chatId как fallback
                    $senderUserId = $chatId;
                    $senderName = "ЦИАН";
                    $firstName = "ЦИАН";
                    $lastName = "Чат {$chatId}";
                }
            } else {
                // Если API вернул ошибку, используем chatId
                p("Ошибка API, используем chatId как ID", "api_error", $log);
                $senderUserId = $chatId;
                $senderName = "ЦИАН";
                $firstName = "ЦИАН";
                $lastName = "Чат {$chatId}";
            }
        } else {
            // ============================================================
            // ОБЫЧНОЕ СООБЩЕНИЕ ОТ ПОЛЬЗОВАТЕЛЯ
            // ============================================================
            $senderName = $arParams['message']['users'][0]['name'];
            
            // Разделяем имя на имя и фамилию, если есть пробел
            $nameParts = explode(' ', trim($senderName), 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
        }

        // ============================================================
        // ФОРМИРОВАНИЕ СООБЩЕНИЯ ДЛЯ BITRIX24
        // ============================================================
        $arMessage = [
            // Массив описания пользователя
            'user' => array(
                'id' => $senderUserId,                      // ID пользователя во внешней системе *
                'last_name' => isset($lastName) ? $lastName : '',  // Фамилия
                'name' => isset($firstName) ? $firstName : $senderName,  // Имя
                'skip_phone_validate' => 'Y',               // Отключить валидацию телефона
            ),
            // Массив описания сообщения
            'message' => array(
                'id' => $arParams['message']['chats'][0]['messages'][0]['messageId'], // ID сообщения *
                'date' => time(),                           // Timestamp *
                'disable_crm' => 'N',                       // Не отключать CRM трекер
                'text' => htmlspecialchars($mess),          // Текст сообщения
            ),
            // Файлы (фото объекта)
            'files' => array(
               array('url' => $arParams['message']['offers'][0]['mainPhoto']['url'], 'name' => 'Объект'),
            ),
            // Массив описания чата
            'chat' => array(
                'id' => $arParams['message']['chats'][0]['chatId'],     // ID чата *
                'name' => $arParams['message']['chats'][0]['offerId'],  // Имя чата (ID объявления)
                'url' => $arParams['message']['offers'][0]['url']       // Ссылка на объявление
            ),
        ];
        
        p($arMessage , "arMessage", $log);
        
        // Получение ID линии Open Lines
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
        
        // ============================================================
        // ОТПРАВКА СООБЩЕНИЯ В BITRIX24
        // ============================================================
        $arMessageSend = $auth->CScore->call(
            'imconnector.send.messages',
            [
                'CONNECTOR' => "cs_cian_connector",
                'LINE' => $lineId,
                'MESSAGES' => [$arMessage],
            ]
        );
        p($arMessageSend , "arMessageSend", $log);
    }
}
