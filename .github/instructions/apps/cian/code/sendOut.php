<?php
/**
 * CIAN Messenger - Отправка исходящих сообщений
 * 
 * Файл: local/components/messager/cian_messager/templates/desctop/sendOut.php
 * Назначение: Отправка сообщений агентов из Bitrix24 в CIAN
 * 
 * Особенности:
 * - Очистка BB-кодов Bitrix24
 * - Отправка в CIAN API
 * - Подтверждение доставки
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logSendOut.txt";
p($arParams, date('d-m-Y-H-i-s'), $log);

if ($arParams['app'] && $arParams['auth']['member_id']) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
    
    // Получение настроек (API ключ)
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [
            'PROPERTY_CS_CIAN_LINE'=> $arParams['data']['LINE']
        ],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0]['PROPERTY_VALUES'];

    // ============================================================
    // ПОДГОТОВКА СООБЩЕНИЯ ДЛЯ CIAN
    // ============================================================
    // Bitrix24 форматирует сообщения BB-кодами: [b]текст[/b], [url]ссылка[/url]
    // CIAN их не понимает, поэтому удаляем
    
    $dataMes = [
        "chatId"=> $arParams['data']['MESSAGES'][0]['chat']['id'],
        "content"=>[
            // Регулярное выражение удаляет все [любой_код]
            "text" => preg_replace('/\[[^\]]*\]/', '', $arParams['data']['MESSAGES'][0]['message']['text'])
        ]
    ];
    
    p($dataMes , "dataMes", $log);
    
    // ============================================================
    // ОТПРАВКА В CIAN API
    // ============================================================
    $keyCian = "Authorization: Bearer ".$resSetup['CS_KEY_CIAN'];

    $ch = curl_init('https://public-api.cian.ru/v1/send-message');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyCian));
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
    // ПОДТВЕРЖДЕНИЕ ДОСТАВКИ В BITRIX24
    // ============================================================
    // Если CIAN вернул messageId, значит отправка успешна
    if($result['result']['messageId']){
        $resultDelivery = $auth->CScore->call(
            'imconnector.send.status.delivery',
            $arParams['data']
        );
        p($resultDelivery , "resultDelivery", $log);
    }
}
