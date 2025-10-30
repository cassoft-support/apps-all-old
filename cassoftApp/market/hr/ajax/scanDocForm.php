<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$log = __DIR__ . "/logScanDoc.txt";
p('start', 'scan', $log);
p($_REQUEST, 'REQUEST', $log);
$arParams = $_REQUEST;
$option=json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $CloudApp = 'hr_pro';
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    if ($clientsApp["ID"] > 0 && $_REQUEST['PLACEMENT'] == 'USERFIELD_TYPE') {
        
        if ($option['ENTITY_VALUE_ID'] > 0) {
            if ($option['ENTITY_ID'] === "CRM_DEAL") {
                $arParams = $_REQUEST;
                $arParams['deal_id'] = $option['ENTITY_VALUE_ID'];
                $arParams['app'] = $CloudApp;
                $arParams['type'] ='deal';
                $APPLICATION->IncludeComponent(
                    "scanDoc:base",
                    "deal",
                    $arParams,
                    false
                );
            }   elseif ($option['ENTITY_ID'] === "CRM_CONTACT") {
                $arParams = $_REQUEST;
                $arParams['contact_id'] = $option['ENTITY_VALUE_ID'];
                $arParams['app'] = $CloudApp;
                $arParams['type'] ='contact';
                $APPLICATION->IncludeComponent(
                    "scanDoc:base",
                    "contact",
                    $arParams,
                    false
                );
            }
            elseif ($option['ENTITY_ID'] === "CRM_COMPANY") {
                $arParams = $_REQUEST;
                $arParams['company_id'] = $option['ENTITY_VALUE_ID'];
                $arParams['app'] = $CloudApp;
                $arParams['type'] ='company';
                $APPLICATION->IncludeComponent(
                    "scanDoc:base",
                    "company",
                    $arParams,
                    false
                );
            }
            else {
                $arParams = $_REQUEST;
                $arParams['id'] = $option['ENTITY_VALUE_ID'];
                $arParams['app'] = $CloudApp;
                $arParams['type'] ='smart';
                $APPLICATION->IncludeComponent(
                    "scanDoc:base",
                    "smart",
                    $arParams,
                    false
                );
            }
        }
    }
}

