<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
$log = __DIR__ . "/logAjax.txt";
p($_POST, "start", $log);
if ($_POST) {
    if (!empty($_POST['app'])) {
        $auth = new \CSlibs\B24\Auth\Auth($_POST['app'], [], $_POST['auth']['member_id']);

        $profileName = $_POST['profile_name'];
        $profile = translit($_POST['profile_name']);
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
$fileInfo = pathinfo(basename(__FILE__))["filename"];
$resName = explode("_", $fileInfo);
 $memberId = $resName[0];
 $ProfileId = $resName[1];

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
        $arParams["profile_id"] = $ProfileId;
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
 $tokenWappi = tokenWappi();


        $postData=[
            'name'=>$profile,
            'webhook_url'=>ADDRESS_SITE.$fileName
        ];
        $urlPost = http_build_query($postData);
     $profileAddWappi =  sendPostWappi('/api/profile/add?'.$urlPost, []);
        p($profileAddWappi , "profileAddWappi", $log);
        if(!empty($profileAddWappi['profile_id']) && $profileAddWappi['status'] === "done") {
            $data=["authorization_status", "incoming_message", "delivery_status", "outgoing_message_phone", "outgoing_message_api", "incoming_call", "application_status"];

            $profileWebHukWappi =  sendPostWappi('/api/webhook/types/set?profile_id='.$profileAddWappi['profile_id'], $data);
p($profileWebHukWappi , "profileWebHukWappi", $log);

            $resCsCode = generateCodeCS();
            $params = [
                'UF_DATE_CREATE' => strtotime(date('c')),
                'UF_DATE_CLOSE' => strtotime('+30 days', strtotime(date('c'))),
                'UF_CS_CODE' => $resCsCode,
                'UF_PROFILE_NAME' => $profile,
                'UF_PROFILE_ID' => $profileAddWappi['profile_id'],
                'UF_MEMBER_ID' => $_POST['auth']['member_id'],
                'UF_DOMAIN' => $_POST['auth']['domain'],
                'UF_ACTIVE' => 'Y',
                'UF_RESOURCE' => 'wappi',
                'UF_TYPE' => 'whatsapp',
                'UF_NAME' => $profileName,

            ];
           p($params , "params", $log);
            $profileHb = new \CSlibs\B24\HL\HlService('app_mcm_profile');

            $profileAdd = $profileHb->elementAdd($params);
            p($profileAdd , "profileAdd", $log);
            if ($profileAdd> 0){
                echo 'Y';
            }
        }



    }
}