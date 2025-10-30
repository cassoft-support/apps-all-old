<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$log = __DIR__ . "/logScanDoc.txt";
p($_REQUEST, 'start', $log);
$arParams = $_REQUEST;
$option=json_decode($_REQUEST['PLACEMENT_OPTIONS'], true);
$memberId = $_REQUEST['member_id'];
if ($memberId) {
    $CloudApp = 'scan_doc';
    $appAccess = 'app_' . $CloudApp . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);
    if ($clientsApp["ID"] > 0 && $_REQUEST['PLACEMENT'] == 'USERFIELD_TYPE') {
        
        if ($option['ENTITY_VALUE_ID'] > 0) {
            if ($option['ENTITY_ID'] === "CRM_DEAL") {
                $type ='deal';
              //  $template='crm';
            }   elseif ($option['ENTITY_ID'] === "CRM_LEAD") {
                $type ='lead';
             //   $template='crm';
            }   elseif ($option['ENTITY_ID'] === "CRM_CONTACT") {
                $type ='contact';
              //  $template='crm';
            }
            elseif ($option['ENTITY_ID'] === "CRM_COMPANY") {
                $type ='company';
              //  $template='crm';
            }
            elseif ($option['ENTITY_ID'] === "CRM_QUOTE") {
                $type ='quote';
               // $template='crm';
            }
            else {
$type='smart';
            }
            $template='crm';
            $arParams = $_REQUEST;
            $arParams['id'] = $option['ENTITY_VALUE_ID'];
            $arParams['app'] = $CloudApp;
            $arParams['type'] =$type;
            $APPLICATION->IncludeComponent(
                "scanDoc:base",
                $template,
                $arParams,
                false
            );

        }
    }
}else{
    ?>
    <div class="">ОБНОВИТЕ СТРАНИЦУ</div>
<?php }

