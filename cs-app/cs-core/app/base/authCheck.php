<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
$_SERVER["DOCUMENT_ROOT"];
require_once("/var/www/bitirx-brokci/data/www/app.cassoft.ru/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_POST, "start", $log);
p($_SERVER["DOCUMENT_ROOT"], "doc", $log);


    $input = file_get_contents('php://input');
    p($input , "input", $log);
    $data = json_decode($input, true);
//$memberId = $data['member_id']."1";
    $memberId = $data['member_id'];
    $app = $data['app'];
    $app_access = $data['app_access'];


p($memberId , "memberId", $log);
if ($memberId && $app_access) {
    p($app_access , "app_access", $log);
    p($app , "app", $log);
    $appAccess = 'app_' . $app_access . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchAppID($memberId, $app);
    p($clientsApp , "clientsApp", $log);
    if ($clientsApp["ID"] > 0) {

            p('res' , "Y", $log);
            echo 'Y';

    }else{
        echo 'N';
    }
}