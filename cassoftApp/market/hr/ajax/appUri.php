<?php


define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$log = __DIR__ . "/logAppUrl.txt";
p('start', 'start', $log);
p($_REQUEST, 'REQUEST', $log);
if ($_REQUEST['PLACEMENT'] === 'REST_APP_URI') {
    $placementOption = json_decode(htmlspecialchars_decode($_REQUEST['PLACEMENT_OPTIONS']), true);
    $options = json_decode($placementOption, true);
}
//d($_REQUEST);
$memberId = $_REQUEST['member_id'];
if ($memberId && $options) {
    $appAccess = 'app_' . $options['app'] . '_access';
    $HlClientApp = new \CSlibs\B24\HL\HlService($appAccess);
    $clientsApp = $HlClientApp->searchID($memberId);

    if ($clientsApp["ID"] > 0) {
        $typeApp = $options['appType'];
//d($typeApp);
        $arParams = $_REQUEST;
        $arParams['app'] = $options['app'];
        $arParams['smartId'] = $options['smartId'];
        $arParams['candidateId'] = $options['candidateId'];
        if($typeApp ==='candidates'){
            $APPLICATION->IncludeComponent(
                "hr:candidates",
                "formSlider",
                $arParams,
                false
            );
        }
        elseif($typeApp ==='vacancy'){
            $APPLICATION->IncludeComponent(
                "hr:vacancy",
                "logistics_pro",
                $arParams,
                false
            );
        }
        else{
        $APPLICATION->IncludeComponent(
            "hr:applications",
            "formSlider",
            $arParams,
            false
        );
    }
    }
}