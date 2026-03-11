<?php
/**
 * CIAN Messenger - Активация интеграции
 * 
 * Файл: local/components/settings/cian_messager/templates/desctop/ajax/ajax.php
 * Назначение: Подписка на webhooks CIAN и активация коннектора в Bitrix24
 * 
 * Процесс:
 * 1. Создание dynamic webhook endpoint файла для клиента
 * 2. Подписка на webhooks CIAN
 * 3. Активация коннектора в Bitrix24
 * 4. Сохранение настроек в Entity
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);

if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);

        // ============================================================
        // ЭТАП 1: СОЗДАНИЕ WEBHOOK ENDPOINT ФАЙЛА
        // ============================================================
        // Каждому клиенту создается уникальный endpoint:
        // /cassoftApp/market/cianMessager/in/{member_id}_{line_id}.php
        
        $fileName = '/cassoftApp/market/cianMessager/in/'.$_POST['auth']['member_id']."_".$_POST['line'].".php";
        $fileAdd = $_SERVER['DOCUMENT_ROOT'] .$fileName;
        
        // Контент webhook обработчика
        $content = ' <?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logHandler.txt";
$message = file_get_contents("php://input");
$result=json_decode($message, true);
p($result, "start", $log);

// Извлечение member_id и line_id из имени файла
$fileInfo = pathinfo(basename(__FILE__))["filename"];
$resName = explode("_", $fileInfo);
$memberId = $resName[0];
$lineId = $resName[1];

p($memberId, "memberId", $log);
if ($memberId) {
    $CloudApp = "cian_messager";
    $appAccess = "app_" . $CloudApp . "_access";
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
       $arParams["message"] = $result;
        $arParams["tempList"] = "sendIn";
        $arParams["app"] = $CloudApp;
        $arParams["member_id"] = $memberId;
        $arParams["line"] = $lineId;
        $APPLICATION->IncludeComponent(
            "messager:cian_messager",
            "desctop",
            $arParams,
            false
        );
    }
}
    ';

        // Проверка и создание файла
        if (!file_exists($fileAdd)) {
            if (file_put_contents($fileAdd, $content) !== false) {
                p("Файл ".$fileName." успешно создан", "add", $log);
            } else {
                p("Ошибка при создании файла ".$fileName, "addError", $log);
            }
        } else {
            p("Файл ".$fileName." уже существует", "res", $log);
        }
        
        // ============================================================
        // ЭТАП 2: ПОДПИСКА НА WEBHOOKS CIAN
        // ============================================================
        $keyCian = "Authorization: Bearer ".$_POST['key'];
        p($keyCian , "keyCian", $log);
        
        $data = array(
            "url" => 'https://app.cassoft.ru'.$fileName,
            "webhookTypes" => [
                "offersMessagesIncoming",       // Входящие сообщения по объявлениям
                "newbuildingMessagesIncoming",  // Входящие сообщения по новостройкам
                "chatsReadability"              // Статусы прочтения чата
            ]
        );
        
        p($data , "data", $log);
        
        $ch = curl_init('https://public-api.cian.ru/v2/subscribe-webhooks');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyCian));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        
        $resAddWebhook = json_decode($res, true);
        p($resAddWebhook , "resAddWebhook", $log);
        
        // ============================================================
        // ЭТАП 3: АКТИВАЦИЯ КОННЕКТОРА В BITRIX24
        // ============================================================
        if(empty($resAddWebhook["result"]["errors"])) {
            $options = json_decode($_POST['options'], true);
            p($options, "options", $log);
            
            $paramsConnector = [
                'CONNECTOR' => $options['CONNECTOR'],  // cs_cian_connector
                'LINE' => $options['LINE'],            // ID Open Line
                'ACTIVE' => 1,                         // 1 = активировать
            ];
            p($paramsConnector , "paramsConnector", $log);
            
            $activate = $auth->CScore->call('imconnector.activate', $paramsConnector);
            p($activate , "activate", $log);
            
            // ============================================================
            // ЭТАП 4: СОХРАНЕНИЕ НАСТРОЕК В ENTITY
            // ============================================================
            $paramsUp = [
                'ENTITY' => 'setup_messager',
                'ID' => $_POST['id'],
                'PROPERTY_VALUES'=>[
                    'CS_KEY_CIAN'  => $_POST['key'],               // API ключ CIAN
                    'CS_CIAN_LINE' => intVal($options['LINE']),    // ID линии
                    'CS_CIAN_CONNECT'=> intVal($options['ACTIVE_STATUS']),  // Статус подключения
                ]
            ];

            $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
            p($resSetupUp , "resSetupUp", $log);
            
            if ($resSetupUp[0] == 1){
                echo 'Y';  // Успех
            }
        } else {
            // Ошибка подписки на webhooks
            echo $resAddWebhook["result"]["errors"][0]["message"];
        }
    }
}
