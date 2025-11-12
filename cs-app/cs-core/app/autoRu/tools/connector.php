<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

$log = __DIR__ . "/logConnector.txt";

$input = file_get_contents('php://input');
p($input, "start", $log);
p($_SERVER['DOCUMENT_ROOT'], "SERVER_DOCUMENT_ROOT", $log);
$data = json_decode($input, true);
p($data , "data", $log);

    if (!empty($data['app_code'])) {
        $fileName = '/cassoftApp/market/mcm/in/'.$data['member_id']."_".$data['profile'].".php";
        $fileAdd = $_SERVER['DOCUMENT_ROOT'] .$fileName;
        p($fileAdd , "fileAdd", $log);
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
$profile = $resName[1];

p($memberId, "memberId", $log);
if ($memberId) {
    
    $appAccess = "app_mcm_access";
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchAppID($memberId, "'.$data['app_code'].'");
    p($clientsApp, "rest", $log);
    if ($clientsApp["ID"] > 0) {
       $arParams["message"] = $result;
        $arParams["tempList"] = "sendIn";
        $arParams["app"] = "mcm";
        $arParams["app_code"] = '.$data['app_code'].';
        $arParams["member_id"] = $memberId;
        $arParams["profile"] = $profile;
        $APPLICATION->IncludeComponent(
            "messager:autoRu",
            "desctop",
            $arParams,
            false
        );
    }
}
    ';

        p(file_exists($fileAdd), "add", $log);
//// Проверяем, существует ли файл
        if (!file_exists($fileAdd)) {
//        // Если файл не существует, создаем его и записываем информацию
            if (file_put_contents($fileAdd, $content) !== false) {
               echo "Y";
            } else {
                p("Ошибка при создании файла ".$fileName, "addError", $log);
            }
        } else {
            p("Файл ".$fileName." существует", "res", $log);
        }

}