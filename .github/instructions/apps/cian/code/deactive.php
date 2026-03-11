<?php
/**
 * CIAN Messenger - Деактивация интеграции
 * 
 * Файл: local/components/settings/cian_messager/templates/desctop/ajax/deactive.php
 * Назначение: Отписка от webhooks CIAN и деактивация коннектора в Bitrix24
 * 
 * Процесс:
 * 1. Отписка от webhooks CIAN
 * 2. Деактивация коннектора в Bitrix24
 * 3. Обновление настроек в Entity
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logDeactive.txt";
p($_POST, "start", $log);
p(ADDRESS_SITE_BROKCI, "ADDRESS_SITE_BROKCI", $log);

if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);
        $options = json_decode($_POST['options'], true);
        
        // ============================================================
        // ПОИСК WEBHOOK ENDPOINT ФАЙЛА
        // ============================================================
        // Проверяем оба варианта имени файла:
        // 1. Старый формат: {member_id}.php
        // 2. Новый формат: {member_id}_{line_id}.php
        
        $fileName = ADDRESS_SITE_BROKCI.'/cassoftApp/market/cianMessager/in/'.$_POST['auth']['member_id'].".php";
        if (file_exists($fileName)) {
            $handler = $fileName;
        }
        
        $fileNameNew = ADDRESS_SITE_BROKCI.'/cassoftApp/market/cianMessager/in/'.$_POST['auth']['member_id']."_".$options['LINE'].".php";
        if (file_exists($fileNameNew)) {
            $handler = $fileNameNew;
        }

        // ============================================================
        // ЭТАП 1: ОТПИСКА ОТ WEBHOOKS CIAN
        // ============================================================
        $keyCian = "Authorization: Bearer ".$_POST['key'];
        p($keyCian , "keyCian", $log);
        
        $data = array(
            "url" => $handler,
            "webhookTypes" => [
                "offersMessagesIncoming",       // Объявления
                "newbuildingMessagesIncoming",  // Новостройки
                "chatsReadability"              // Статусы прочтения
            ]
        );

        $ch = curl_init('https://public-api.cian.ru/v2/unsubscribe-webhooks');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyCian));
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
        // ЭТАП 2: ДЕАКТИВАЦИЯ КОННЕКТОРА В BITRIX24
        // ============================================================
        if(empty($resAddWebhook["result"]["errors"])) {
            
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
            // ЭТАП 3: ОБНОВЛЕНИЕ НАСТРОЕК В ENTITY
            // ============================================================
            $paramsUp = [
                'ENTITY' => 'setup_messager',
                'ID' => $_POST['id'],
                'PROPERTY_VALUES'=>[
                    'CS_CIAN_CONNECT'=> false,  // Отключено
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
