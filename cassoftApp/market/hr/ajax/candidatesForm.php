<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$log = __DIR__ . "/logCandidates.txt";
p('start', 'scan', $log);
p($_REQUEST, 'REQUEST', $log);
$arParams = $_REQUEST;
$option=json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
p($option, 'option', $log);
$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $CloudApp = 'hr_pro';
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    if ($clientsApp["ID"] > 0 && $_REQUEST['PLACEMENT'] == 'USERFIELD_TYPE') {
        
        if ($option['ENTITY_VALUE_ID'] > 0) {
            $arParams = $_REQUEST;
            $arParams['app'] = $CloudApp;
            if ($option['ENTITY_ID'] === "CRM_DEAL") {
                $arParams['deal_id'] = $option['ENTITY_VALUE_ID'];
                $arParams['type'] ='deal';
                $APPLICATION->IncludeComponent(
                    "hr:candidatesForm",
                    "deal",
                    $arParams,
                    false
                );
            }   elseif ($option['ENTITY_ID'] === "CRM_CONTACT") {
                $arParams['contact_id'] = $option['ENTITY_VALUE_ID'];
                $arParams['type'] ='contact';
                $APPLICATION->IncludeComponent(
                    "hr:candidatesForm",
                    "contact",
                    $arParams,
                    false
                );
            }
            elseif ($option['ENTITY_ID'] === "CRM_COMPANY") {
                $arParams['company_id'] = $option['ENTITY_VALUE_ID'];
                $arParams['type'] ='company';
                $APPLICATION->IncludeComponent(
                    "hr:candidatesForm",
                    "company",
                    $arParams,
                    false
                );
            }
            else {
                $arParams['id'] = $option['ENTITY_VALUE_ID'];
                $arParams['EntityId'] = $option['ENTITY_ID'];
                $arParams['type'] ='smart';
                $APPLICATION->IncludeComponent(
                    "hr:candidatesForm",
                    "smart",
                    $arParams,
                    false
                );
            }
        }
    }
}

