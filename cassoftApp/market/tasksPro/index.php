<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logIndex.txt";
p($_REQUEST , "start", $log);
$arParams = $_REQUEST;
$option=json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $arParams['app'] = 'tasks_pro';
    $appAccess = 'app_' . $arParams['app'] . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    if ($clientsApp["ID"] > 0) {

        //   $arParams['app'] = 'logistics_pro';
//if($_REQUEST['PLACEMENT'] === 'CRM_LEAD_DETAIL_TAB'
//    || $_REQUEST['PLACEMENT'] === 'CRM_DEAL_DETAIL_TAB'
//    || $_REQUEST['PLACEMENT'] === 'CRM_QUOTE_DETAIL_TAB'
//    ){
//    $APPLICATION->IncludeComponent(
//        "dashboard:main_app",
//        $arParams['app'],
//        $arParams,
//        false
//    );
//}
//elseif($_REQUEST['PLACEMENT'] === 'CRM_COMPANY_DETAIL_TAB'
//) {
//
//    $arParams['templateType'] = 'company';
//    $APPLICATION->IncludeComponent(
//        "dashboard:main_app",
//        "logistics_pro",
//        $arParams,
//        false
//    );
//}
//else {

        $arParams['templateType']='vertical';
        $APPLICATION->IncludeComponent(
            "dashboard:main_app",
            $arParams['app'],
            $arParams,
            false
        );
    }
}