<?php
/**
 * Domclick Messenger - Отправка исходящих сообщений
 * 
 * Файл: local/components/messager/domclick_messager/templates/desctop/sendOut.php
 * Назначение: Отправка сообщений агентов из Bitrix24 в Domclick
 * 
 * Особенности:
 * - Очистка BB-кодов Bitrix24 (аналогично CIAN)
 * - Структура запроса: chat_id + message (отличие от CIAN)
 * - Endpoint: /chats/v1/messages/ (отличие от CIAN)
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logSendOut.txt";
p($arParams, 'start', $log);

if ($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
    
    // ============================================================
    // ЭТАП 1: ПОЛУЧЕНИЕ НАСТРОЕК (API КЛЮЧ)
    // ============================================================
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [
            'PROPERTY_CS_DC_LINE'=> $arParams['data']['LINE']
        ],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0]['PROPERTY_VALUES'];
    p($resSetup, "resSetup", $log);

    // ============================================================
    // ЭТАП 2: ПОДГОТОВКА СООБЩЕНИЯ ДЛЯ DOMCLICK
    // ============================================================
    // Bitrix24 форматирует сообщения BB-кодами: [b]текст[/b], [url]ссылка[/url]
    // Domclick их не понимает, поэтому удаляем
    
    $dataMes = [
        "chat_id" => $arParams['data']['MESSAGES'][0]['chat']['id'],  // Отличие от CIAN: chat_id
        "message" => preg_replace('/\[[^\]]*\]/', '', $arParams['data']['MESSAGES'][0]['message']['text'])  // Очистка BB-кодов
    ];
    
    p($dataMes , "dataMes", $log);
    
    // ============================================================
    // ЭТАП 3: ОТПРАВКА В DOMCLICK API
    // ============================================================
    $keyDC = "Authorization: Bearer ".$resSetup['CS_KEY_DC'];

    $ch = curl_init('https://public-api.domclick.ru/chats/v1/messages/');  // Отличие от CIAN endpoint
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyDC, 'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataMes, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($res, true);
    p($result , "result", $log);
    
    // ============================================================
    // ЭТАП 4: ПОДТВЕРЖДЕНИЕ ДОСТАВКИ В BITRIX24
    // ============================================================
    // Если Domclick вернул success: true, значит отправка успешна
    if($result['success']){
        $resultDelivery = $auth->CScore->call(
            'imconnector.send.status.delivery',
            $arParams['data']
        );
        p($resultDelivery , "resultDelivery", $log);
    }
}
