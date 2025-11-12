<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
//require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$log = __DIR__ . "/logComp.txt";
p($arParams, "start", $log);
p($_REQUEST, "REQUEST", $log);

////pr($arParams['app'], '');
if (!empty($arParams['member_id']) && !empty($arParams['app'])) {
 $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
//pr($auth, '');
     $user = $auth->CScore->call("user.current");
    //    pr($user, '');
        if ($user) {
            $arResult['userFIO'] = $user['LAST_NAME'];
            $arResult['user_id'] = $user['ID'];
        }
    $arResult["app"] = $arParams['app'];
    $arResult["deal_id"] = $arParams['deal_id'];
    $arResult['admin'] = "N";
    $arResult['domain'] = $_REQUEST['DOMAIN'];
    $arResult['member_id'] = $arParams['member_id'];
   

   $resSetup = $auth->CScore->call('entity.item.get', ['ENTITY' => 'setup', 'SORT' => [], 'FILTER' => []])[0];
    $adminUsers = [];
    if (!empty($resSetup['PROPERTY_VALUES']['CS_CHAT_BUY'])) {
        $arResult['chatBuy'] = json_decode($resSetup['PROPERTY_VALUES']['CS_CHAT_BUY'], true);
    }else{
    $arResult['chatBuy'] ="(function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
    })(window,document,'https://cdn-ru.bitrix24.ru/b9950371/crm/site_button/loader_5_9bynjt.js');";
}
    if (!empty($resSetup['PROPERTY_VALUES']['CS_CHAT_SUPPORT'])) {
        $arResult['chatSupport'] = json_decode($resSetup['PROPERTY_VALUES']['CS_CHAT_SUPPORT'], true);
    }else{
        $arResult['chatSupport'] ="(function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
    })(window,document,'https://cdn-ru.bitrix24.ru/b9950371/crm/site_button/loader_5_9bynjt.js');";
    }
//    if (!empty($resSetup['PROPERTY_VALUES']['UF_CS_ADMIN'])) {
//        $adminUsers = json_decode(htmlspecialchars_decode($resSetup['PROPERTY_VALUES']['UF_CS_ADMIN']), true);
//    }
//    if (!empty($adminUsers))
//        if (in_array($user['ID'], $adminUsers)) {
//            $arResult['admin'] = "Y";
//        }
//    $marketingUsers = (!empty($resSetup['PROPERTY_VALUES']['UF_USERS_MARKETING'])) ? json_decode(
//        htmlspecialchars_decode($resSetup['PROPERTY_VALUES']['UF_USERS_MARKETING']),
//        true
//    ) : [];
//    $plansUsers = (!empty($resSetup['PROPERTY_VALUES']['UF_USERS_PLANS'])) ? json_decode(
//        htmlspecialchars_decode($resSetup['PROPERTY_VALUES']['UF_USERS_PLANS']),
//        true
//    ) : [];
//    if ($marketingUsers) {
//        $arResult['marketing'] = (in_array($user['ID'], $marketingUsers, true) || $arResult['admin'] === 'Y') ? true : false;
//    }
//    if ($plansUsers) {
//        $arResult['plans'] = (in_array($user['ID'], $plansUsers, true) || $arResult['admin'] === 'Y') ? true : false;
//    }
    if ($_REQUEST['PLACEMENT']) {
        $arResult['PLACEMENT'] = $arParams['PLACEMENT'];
    }
    if ($arParams['templateType']) {
        $templateType = $arParams['templateType'];
    }

    p($templateType, "templateType", $log);
    p($arResult, "arResult", $log);

    $this->IncludeComponentTemplate($templateType);
}