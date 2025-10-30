<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);
    $fileName = '/cassoftApp/market/domclickMessager/in/'.$_POST['auth']['member_id'].".php";
    $fileAdd = $_SERVER['DOCUMENT_ROOT'] .$fileName;
    $content = ' <?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$log = __DIR__."/logHandler.txt";
$message = file_get_contents("php://input");
$result=json_decode($message, true);
p($result, "start", $log);
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
      $keyAuth = "Authorization: Bearer ".$_POST['key'];
p($keyAuth , "keyAuth", $log);
        $data = array(
  "url" => ADDRESS_SITE.$fileName,
  "types"=> [
            "new_messages"
        ],
  "description"=> "CS domclick"
        );

        p($data , "data", $log);
 $ch = curl_init('https://public-api.domclick.ru/chats/v1/webhooks/'); //подписаться

        curl_setopt(  $ch,CURLOPT_HTTPHEADER,array($keyAuth, 'Content-Type: application/json' ));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        $resAddWebhook = json_decode($res, true);
        p($resAddWebhook , "resAddWebhook", $log);
if(empty($resAddWebhook["errors"])) {
    $options = json_decode($_POST['options'], true);
    p($options, "options", $log);
        $activate = $auth->CScore->call(
            'imconnector.activate',
            [
                'CONNECTOR' => $options['CONNECTOR'],
                'LINE' => intVal($options['LINE']),
                'ACTIVE' => intVal($options['ACTIVE_STATUS']),
            ]
        );
        p($activate , "activate", $log);
    $paramsUp = [
        'ENTITY' => 'setup_messager',
        'ID' => $_POST['id'],
        'PROPERTY_VALUES'=>[
            'CS_KEY_DC'  => $_POST['key'],
            'CS_DC_LINE' => intVal($options['LINE']),
            'CS_DC_CONNECT'=> intVal($options['ACTIVE_STATUS']),
        ]
    ];


    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
    p($resSetupUp , "resSetupUp", $log);
    if ($resSetupUp[0] == 1){
        echo 'Y';
    }
}else{
    echo 'N';
}

}