<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logDeactive.txt";
p($_POST, "start", $log);
if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);

        $fileName = '/cassoftApp/market/domclickMessager/in/'.$_POST['auth']['member_id'].".php";
        $keyAuth = "Authorization: Bearer ".$_POST['key'];
        $data = array(
            "url" => ADDRESS_SITE.$fileName,
        );
p($data , "data", $log);
       $ch = curl_init('https://public-api.domclick.ru/chats/v1/webhooks/unsubscribe'); //отписаться

        curl_setopt(  $ch,CURLOPT_HTTPHEADER,array($keyAuth, 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        $resAddWebhook = json_decode($res, true);
        p($resAddWebhook , "res", $log);
if(empty($resAddWebhook["errors"])) {
    $options = json_decode($_POST['options'], true);
    p($options, "options", $log);
        $activate = $auth->CScore->call(
            'imconnector.activate',
            [
                'CONNECTOR' => $options['CONNECTOR'],
                'LINE' => intVal($options['LINE']),
                'ACTIVE' => 0,
            ]
        );
        p($activate , "activate", $log);
    $paramsUp = [
        'ENTITY' => 'setup_messager',
        'ID' => $_POST['id'],
        'PROPERTY_VALUES'=>[
            'CS_KEY_CIAN'  => $_POST['key'],
            'CS_CIAN_LINE' => false,
            'CS_CIAN_CONNECT'=> false,
        ]
    ];


    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
    p($resSetupUp , "resSetupUp", $log);
    if ($resSetupUp[0] == 1){
        echo 'Y';
    }
}


    }
}