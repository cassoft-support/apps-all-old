<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] ."/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_POST, "start", $log);

if($_REQUEST){
    $arParams = $_REQUEST;
    $option=json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
    $memberId = $_REQUEST['member_id'];
}else{
    $input = file_get_contents('php://input');
    p($input , "input", $log);
    $data = json_decode($input, true);
//$memberId = $data['member_id']."1";
   $memberId = $data['member_id'];
}


p($memberId , "memberId", $log);
if ($memberId) {
    $arParams['app'] = 'mcm';
    $appAccess = 'app_' . $arParams['app'] . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    p($clientsApp , "clientsApp", $log);
    if ($clientsApp["ID"] > 0) {
if(!empty($_REQUEST)){
    p($arParams , "arParams", $log);
    $arParams['templateType']='vertical';
    $APPLICATION->IncludeComponent(
        "dashboard:main_app",
        $arParams['app'],
        $arParams,
        'vertical'
    );
}else{
    p('res' , "Y", $log);
    echo 'Y';
}

    }else{
        echo 'N';
    }
}