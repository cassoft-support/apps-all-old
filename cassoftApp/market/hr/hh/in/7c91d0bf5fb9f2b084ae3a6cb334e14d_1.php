 <?php
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
 $userId = $resName[1];
 p($memberId, "memberId", $log);
 if ($memberId) {
     $CloudApp = "hr_pro";
     $appAccess = "app_" . $CloudApp . "_access";
     $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
     $clientsApp = $HlClientApp->searchID($memberId);
     p($clientsApp, "rest", $log);
     if ($clientsApp["ID"] > 0) {
         $arParams["app"] = $CloudApp;
         $arParams["result"] = $result;
         $arParams["member_id"] = $memberId;
         $arParams["userId"] = $userId;
        $APPLICATION->IncludeComponent(
            "event:hr",
            "desctop",
            $arParams,
            false
        );
    }
}
    