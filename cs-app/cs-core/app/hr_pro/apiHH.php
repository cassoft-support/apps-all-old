<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once("/var/www/www-root/data/www/app.cassoft.ru/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logApiHH.txt";

$input = file_get_contents('php://input');
p($input, "start", $log);
$data = json_decode($input, true);
p($data , "data", $log);

if(!empty($data)) {
    p($data['fn'], "fn", $log);
    if ($data['fn'] === 'hhKey') {
        p($data, "data", $log);
        $res = hhKey($data['app']);
        p($res, "res", $log);
        echo json_encode($res);
        exit;
    } else if($data['fn'] === 'handlerAdd'){
        p($data, "data", $log);
        $res = handlerAdd($data['member_id'], $data['userId']);
        p($res, "res", $log);
        echo $res;
        exit;
    }
}
function hhKey($app){
$HlApp = new \CSlibs\B24\HL\HlService('app_auth_params');
$appParams = $HlApp->hl::getList([
'select' => ['*'],
'order' => [],
'filter' => [
'UF_APP_NAME' => $app
],
'limit' => 1
])->fetch();
$resKey['ID']= $appParams['UF_HH_ID'];
$resKey['KEY']= $appParams['UF_HH_KEY'];
return $resKey;
}

function handlerAdd($member_id, $userId){
    $log = __DIR__."/logHandlerAdd.txt";
    $fileName = '/cassoftApp/market/hr/hh/in/'.$member_id."_".$userId.".php";
    $fileAdd = $_SERVER['DOCUMENT_ROOT'] .$fileName;
    p($fileAdd , "start", $log);
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
$Id = $resName[1];
p($memberId, "memberId", $log);
if ($memberId) {
    $CloudApp = "hr_pro";
    $appAccess = "app_" . $CloudApp . "_access";
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
        $arParams["message"] = $result;
        $arParams["tempList"] = "sendIn";
        $arParams["app"] = $CloudApp;
        $arParams["member_id"] = $memberId;
        $arParams["id"] = $Id;
        $APPLICATION->IncludeComponent(
            "event:hr",
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
            return("https://app.cassoft.ru".$fileName);
        } else {
            return("error");
            p("Ошибка при создании файла ".$fileName, "addError", $log);
        }
    } else {
        return("https://app.cassoft.ru".$fileName);
    }
}


?>