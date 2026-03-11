<?php
/**
 * Domclick Messenger - Деактивация интеграции
 * 
 * Файл: local/components/settings/domclick_messager/templates/desctop/ajax/deactive.php
 * Назначение: Отписка от webhooks Domclick и деактивация коннектора в Bitrix24
 * 
 * Отличия от CIAN:
 * - Endpoint: /chats/v1/webhooks/unsubscribe (у CIAN: /v2/unsubscribe-webhooks)
 * - Только URL в запросе (без типов вебхуков)
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logDeactive.txt";
p($_POST, "start", $log);

if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);

        // ============================================================
        // ЭТАП 1: ФОРМИРОВАНИЕ URL ENDPOINT
        // ============================================================
        $fileName = '/cassoftApp/market/domclickMessager/in/'.$_POST['auth']['member_id'].".php";
        
        // ============================================================
        // ЭТАП 2: ОТПИСКА ОТ WEBHOOKS DOMCLICK
        // ============================================================
        $keyAuth = "Authorization: Bearer ".$_POST['key'];
        
        $data = array(
            "url" => ADDRESS_SITE.$fileName,  // Только URL (отличие от CIAN)
        );

        p($data , "data", $log);
        
        // Endpoint: /chats/v1/webhooks/unsubscribe (отличие от CIAN)
        $ch = curl_init('https://public-api.domclick.ru/chats/v1/webhooks/unsubscribe');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyAuth, 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        
        $resAddWebhook = json_decode($res, true);
        p($resAddWebhook , "res", $log);
        
        // ============================================================
        // ЭТАП 3: ДЕАКТИВАЦИЯ КОННЕКТОРА В BITRIX24
        // ============================================================
        if(empty($resAddWebhook["errors"])) {
            $options = json_decode($_POST['options'], true);
            p($options, "options", $log);
            
            $activate = $auth->CScore->call(
                'imconnector.activate',
                [
                    'CONNECTOR' => $options['CONNECTOR'],
                    'LINE' => intVal($options['LINE']),
                    'ACTIVE' => 0,  // 0 = деактивировать
                ]
            );
            p($activate , "activate", $log);
            
            // ============================================================
            // ЭТАП 4: ОБНОВЛЕНИЕ НАСТРОЕК В ENTITY
            // ============================================================
            $paramsUp = [
                'ENTITY' => 'setup_messager',
                'ID' => $_POST['id'],
                'PROPERTY_VALUES'=>[
                    'CS_KEY_DC'  => $_POST['key'],
                    'CS_DC_LINE' => false,
                    'CS_DC_CONNECT'=> false,  // Отключено
                ]
            ];

            $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
            p($resSetupUp , "resSetupUp", $log);
            
            if ($resSetupUp[0] == 1){
                echo 'Y';  // Успех
            }
        }
    }
}
