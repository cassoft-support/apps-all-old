<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);
if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);

        $profile = 'c35a4dc2-a49b';
        $fileName = '/cassoftApp/market/mcm/in/'.$_POST['auth']['member_id']."_".$profile.".php";
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
    $CloudApp = "mcm";
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
            "messager:mcm",
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

        $HlApp = new \CSlibs\B24\HL\HlService('app_auth_params');
        $tokenWappi = $HlApp->getByFilterList(['UF_APP_NAME'=>'mcm'])[0]['UF_TOKEN_WAPPI'];

      $token = "Authorization: Bearer ".$tokenWappi;
pr($token , "token", $log);
        $data = array(
            "url" => ADDRESS_SITE.$fileName,
            "profile_id" =>  $profile,
//            "webhookTypes" => [
//                "offersMessagesIncoming", //— все входящие сообщения по объявлениям;
//                "chatsReadability" // вебхук о прочитанности чата;
//            ]
        );

//   $ch = curl_init('https://wappi.pro/api/webhook/url/set'); //подписаться
//////        $ch = curl_init('https://public-api.cian.ru/v2/unsubscribe-webhooks'); //отписаться
//////        Array
//////        (
//////            [operationId] => F7D1D4F2-CCDD-11EF-B55B-06BA91E768BD
//////        [result] => [])
//        curl_setopt(  $ch,CURLOPT_HTTPHEADER,array($token ));
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_HEADER, false);
//        $res = curl_exec($ch);
//        curl_close($ch);
//        $resAddWebhook = json_decode($res, true);
//      pr($res , "res",);
//      pr($resAddWebhook , "resAddWebhook", );


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wappi.pro/api/webhook/url/set?profile_id='.$profile.'&url='.ADDRESS_SITE.$fileName,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$tokenWappi
            ),
        ));

        $response = curl_exec($curl);
        $resAddWebhook = json_decode($response, true);
        pr($resAddWebhook , "resAddWebhook", );
        curl_close($curl);
    //    echo $response;

//if(empty($resAddWebhook["result"]["errors"])) {
//    $options = json_decode($_POST['options'], true);
//    p($options, "options", $log);
//        $activate = $auth->CScore->call(
//            'imconnector.activate',
//            [
//                'CONNECTOR' => $options['CONNECTOR'],
//                'LINE' => intVal($options['LINE']),
//                'ACTIVE' => intVal($options['ACTIVE_STATUS']),
//            ]
//        );
//        p($activate , "activate", $log);
//    $paramsUp = [
//        'ENTITY' => 'setup_messager',
//        'ID' => $_POST['id'],
//        'PROPERTY_VALUES'=>[
//            'CS_KEY_CIAN'  => $_POST['key'],
//            'CS_CIAN_LINE' => intVal($options['LINE']),
//            'CS_CIAN_CONNECT'=> intVal($options['ACTIVE_STATUS']),
//        ]
//    ];
//
//
//    $resSetupUp = $auth->CScore->call('entity.item.update', $paramsUp);
//    p($resSetupUp , "resSetupUp", $log);
//    if ($resSetupUp[0] == 1){
//        echo 'Y';
//    }
//}
//    if (!empty($result['result']))
//    {
//        //add data widget
//        if(!empty($widgetUri) && !empty($widgetName))
//        {
//            $resultWidgetData = CRest::call(
//                'imconnector.connector.data.set',
//                [
//                    'CONNECTOR' => $connector_id,
//                    'LINE' => intVal($options['LINE']),
//                    'DATA' => [
//                        'id' => $connector_id.'line'.intVal($options['LINE']),//
//                        'url_im' => $widgetUri,
//                        'name' => $widgetName
//                    ],
//                ]
//            );
//            if(!empty($resultWidgetData['result']))
//            {
//                setLine($options['LINE']);
//                echo 'successfully';
//            }
//        }
//        else
//        {
//            setLine($options['LINE']);
//            echo 'successfully';
//        }
//    }



    }
}