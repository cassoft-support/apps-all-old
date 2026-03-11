<?php
/**
 * Domclick Messenger - Активация интеграции
 * 
 * Файл: local/components/settings/domclick_messager/templates/desctop/ajax/ajax.php
 * Назначение: Подписка на webhooks Domclick и активация коннектора в Bitrix24
 * 
 * Отличия от CIAN:
 * - Один тип webhook: "new_messages" (у CIAN: 3 типа)
 * - Endpoint: /chats/v1/webhooks/ (у CIAN: /v2/subscribe-webhooks)
 * - Добавлено поле "description"
 */

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);

if (!empty($_POST['app'])) {
    $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);
    
    // ============================================================
    // ЭТАП 1: СОЗДАНИЕ WEBHOOK ENDPOINT ФАЙЛА
    // ============================================================
    // Каждому клиенту создается уникальный endpoint:
    // /cassoftApp/market/domclickMessager/in/{member_id}.php
    
    $fileName = '/cassoftApp/market/domclickMessager/in/'.$_POST['auth']['member_id'].".php";
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

// Извлечение member_id из имени файла
$memberId = $fileInfo = pathinfo(basename(__FILE__))["filename"];
p($memberId, "memberId", $log);

if ($memberId) {
    $CloudApp = "domclick_messager";
    $appAccess = "app_" . $CloudApp . "_access";
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        $arParams["message"] = $result;
        $arParams["tempList"] = "sendIn";
        $arParams["app"] = $CloudApp;
        $arParams["member_id"] = $memberId;
        $APPLICATION->IncludeComponent(
            "messager:domclick_messager",
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
        p("Файл ".$fileName." существует", "res", $log);
    }
    
    // ============================================================
    // ЭТАП 2: ПОДПИСКА НА WEBHOOKS DOMCLICK
    // ============================================================
    $keyAuth = "Authorization: Bearer ".$_POST['key'];
    p($keyAuth , "keyAuth", $log);
    
    $data = array(
        "url" => 'https://app.cassoft.ru'.$fileName,
        "types" => [
            "new_messages"  // Только один тип webhook (отличие от CIAN)
        ],
        "description" => "CS domclick"  // Описание (опционально)
    );

    p($data , "data", $log);
    
    // Endpoint: /chats/v1/webhooks/ (отличие от CIAN: /v2/subscribe-webhooks)
    $ch = curl_init('https://public-api.domclick.ru/chats/v1/webhooks/');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($keyAuth, 'Content-Type: application/json'));
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
    if(empty($resAddWebhook["errors"])) {
        $options = json_decode($_POST['options'], true);
        p($options, "options", $log);
        
        $activate = $auth->CScore->call(
            'imconnector.activate',
            [
                'CONNECTOR' => $options['CONNECTOR'],  // cs_domclick_connector
                'LINE' => intVal($options['LINE']),    // ID Open Line
                'ACTIVE' => intVal($options['ACTIVE_STATUS']),  // 1 = активировать
            ]
        );
        p($activate , "activate", $log);
        
        // ============================================================
        // ЭТАП 4: СОХРАНЕНИЕ НАСТРОЕК В ENTITY
        // ============================================================
        $paramsUp = [
            'ENTITY' => 'setup_messager',
            'ID' => $_POST['id'],
            'PROPERTY_VALUES'=>[
                'CS_KEY_DC'  => $_POST['key'],                        // API ключ Domclick
                'CS_DC_LINE' => intVal($options['LINE']),             // ID линии
                'CS_DC_CONNECT'=> intVal($options['ACTIVE_STATUS']),  // Статус подключения
            ]
        ];

        $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
        p($resSetupUp , "resSetupUp", $log);
        
        if ($resSetupUp[0] == 1){
            echo 'Y';  // Успех
        }
    } else {
        // Ошибка подписки на webhooks
        echo json_encode($resAddWebhook["errors"]);
    }
}
