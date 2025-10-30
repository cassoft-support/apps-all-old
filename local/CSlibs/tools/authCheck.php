<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once("/var/www/www-root/data/www/app.cassoft.ru/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_POST, "start", $log);

    $input = file_get_contents('php://input');
    p($input , "input", $log);
    $data = json_decode($input, true);
//$memberId = $data['member_id']."1";
    $memberId = $data['member_id'];
    $app = $data['app'];

p($memberId , "memberId", $log);
if ($memberId && $app) {
    $arParams['app'] = $app;
    $appAccess = 'app_' . $app . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp , "clientsApp", $log);
    if ($clientsApp["ID"] > 0) {
            p('res' , "Y", $log);
            echo 'Y';
    }else{
        echo 'N';
    }
}