<?php
/**
 * Domclick Messenger - Обработка входящих сообщений
 * 
 * Файл: local/components/messager/domclick_messager/templates/desctop/sendIn.php
 * Назначение: Прием webhooks от Domclick и отправка в Bitrix24 Open Lines
 * 
 * Особенности:
 * - Проверка роли пользователя (BUYER/AGENT)
 * - Разделение display_name на имя и фамилию
 * - Без дополнительных API запросов (проще чем CIAN)
 * - Без файлов (Domclick не передает их в webhook)
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logSendIn.txt";
p($arParams, 'start', $log);

if($arParams['app'] && $arParams['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);

    if (!empty($arParams['message']['messages'])) {
        
        // ============================================================
        // ЭТАП 1: ОПРЕДЕЛЕНИЕ РОЛЕЙ ПОЛЬЗОВАТЕЛЕЙ
        // ============================================================
        // Создаем карту: user_id => роль
        foreach ($arParams['message']['users'] as $user) {
            $arUser[$user['user_id']] = $user['roles'][0];
        }
        
        p($arUser , "arUser", $log);
        p($arUser[$arParams['message']['messages'][0]['user_id']] , "arUser role", $log);
        
        // ============================================================
        // ЭТАП 2: ПРОВЕРКА РОЛИ ОТПРАВИТЕЛЯ
        // ============================================================
        // Обрабатываем только сообщения от покупателей (BUYER)
        // Сообщения от агентов (AGENT) игнорируются
        
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
            
            // ============================================================
            // ЭТАП 3: РАЗДЕЛЕНИЕ ИМЕНИ НА ИМЯ И ФАМИЛИЮ
            // ============================================================
            // display_name обычно в формате "Имя Фамилия"
            // Разделяем на две части
            
            $nameParts = explode(' ', trim($sender['display_name']), 2);
            $firstName = $nameParts[0];                                    // Имя (первая часть)
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';       // Фамилия (вторая часть)
            
            // ============================================================
            // ЭТАП 4: ФОРМИРОВАНИЕ СООБЩЕНИЯ ДЛЯ BITRIX24
            // ============================================================
            $arMessage = [
                // Массив описания пользователя
                'user' => array(
                    'id' => $sender['user_id'],                 // ID пользователя во внешней системе *
                    'last_name' => $lastName,                   // Фамилия
                    'name' => $firstName,                       // Имя
                    'picture' => array(),                       // Аватар (пустой)
                    'skip_phone_validate' => 'Y',               // Отключить валидацию телефона
                ),
                // Массив описания сообщения
                'message' => array(
                    'id' => $arParams['message']['messages'][0]['id'],     // ID сообщения *
                    'date' => time(),                                       // Timestamp *
                    'disable_crm' => 'N',                                   // Не отключать CRM трекер
                    'text' => htmlspecialchars($mess),                     // Текст сообщения
                ),
                // Файлы (Domclick не передает файлы в webhook)
                'files' => array(),
                // Массив описания чата
                'chat' => array(
                    'id' => $arParams['message']['messages'][0]['chat_id'], // ID чата *
                    'url' => $arParams['message']['offers'][0]['url']       // Ссылка на объявление
                ),
            ];

            p($arMessage, "arMessage", $log);

            // ============================================================
            // ЭТАП 5: ПОЛУЧЕНИЕ НАСТРОЕК
            // ============================================================
            $params = [
                'ENTITY' => 'setup_messager',
                'sort' => [],
                'filter' => [],
            ];
            $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
            p($resSetup['PROPERTY_VALUES'], "setup", $log);
            
            // ============================================================
            // ЭТАП 6: ОТПРАВКА СООБЩЕНИЯ В BITRIX24
            // ============================================================
            $paramsMessage = [
                'CONNECTOR' => "cs_domclick_connector",
                'LINE' => $resSetup['PROPERTY_VALUES']['CS_DC_LINE'],
                'MESSAGES' => [$arMessage],
            ];
            
            $arMessageSend = $auth->CScore->call('imconnector.send.messages', $paramsMessage);
            p($arMessageSend, "arMessageSend", $log);
        }
    }
}
