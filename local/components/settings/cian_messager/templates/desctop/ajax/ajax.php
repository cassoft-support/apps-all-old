<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);
if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);

    $fileName = '/cassoftApp/market/cianMessager/in/'.$_POST['auth']['member_id']."_".$_POST['line'].".php";
    $fileAdd = $_SERVER['DOCUMENT_ROOT'] .$fileName;
    $content = ' <?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logHandler.txt";
$message = file_get_contents("php://input");
$result=json_decode($message, true);
p($result, "start", $log);
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

//// Проверяем, существует ли файл
if (!file_exists($fileAdd)) {
//        // Если файл не существует, создаем его и записываем информацию
    if (file_put_contents($fileAdd, $content) !== false) {
           p("Файл ".$fileName." успешно создан и в него записана информация", "add", $log);
        } else {
        p("Ошибка при создании файла ".$fileName, "addError", $log);
        }
    } else {
    p("Файл ".$fileName." существует", "res", $log);
    }
      $keyCian = "Authorization: Bearer ".$_POST['key'];
p($keyCian , "keyCian", $log);
        $data = array(
            "url" => 'https://app.cassoft.ru'.$fileName,
            "webhookTypes" => [
                "offersMessagesIncoming", //— все входящие сообщения по объявлениям;
                "chatsReadability" // вебхук о прочитанности чата;
            ]
        );
p($data , "data", $log);
    $ch = curl_init('https://public-api.cian.ru/v2/subscribe-webhooks'); //подписаться

        curl_setopt(  $ch,CURLOPT_HTTPHEADER,array($keyCian ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        $resAddWebhook = json_decode($res, true);
       p($resAddWebhook , "resAddWebhook", $log);
if(empty($resAddWebhook["result"]["errors"])) {
    $options = json_decode($_POST['options'], true);
    p($options, "options", $log);
    $paramsConnector=[
        'CONNECTOR' => $options['CONNECTOR'],
        'LINE' => $options['LINE'],
        'ACTIVE' => 1,
    ];
    p($paramsConnector , "paramsConnector", $log);
        $activate = $auth->CScore->call(
            'imconnector.activate',$paramsConnector
        );
        p($activate , "activate", $log);
    $paramsUp = [
        'ENTITY' => 'setup_messager',
        'ID' => $_POST['id'],
        'PROPERTY_VALUES'=>[
            'CS_KEY_CIAN'  => $_POST['key'],
            'CS_CIAN_LINE' => intVal($options['LINE']),
            'CS_CIAN_CONNECT'=> intVal($options['ACTIVE_STATUS']),
        ]
    ];


    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
    p($resSetupUp , "resSetupUp", $log);
    if ($resSetupUp[0] == 1){
        echo 'Y';
    }
} else {
    echo $resAddWebhook["result"]["errors"][0]["message"];
}


    }
}