<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/CSlibs/vendor/autoload.php");
$file_log = __DIR__ . "/logComp.txt";
file_put_contents($file_log, print_r("compDashboard\n", true));
 file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);
  file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);

if (!empty($arParams['member_id']) && !empty($arParams['app'])) {
    $auth = new \CSlibs\B24\Auth\Auth($arParams['app'], $arParams, "");
    $user = $auth->CScore->call("user.current");
    if ($user) {
        $arResult['userFIO'] = $user['LAST_NAME'];
        $arResult['user_id'] = $user['ID'];
    }
    $arResult["app"] = $arParams['app'];
    $arResult["deal_id"] = $arParams['deal_id'];
    $arResult['admin'] = "N";
    $arResult['domain'] = $_REQUEST['DOMAIN'];
    $arResult['member_id'] = $arParams['member_id'];
    $arResult['UserAut'] = json_encode($_REQUEST);
    $arResult['req'] = json_encode($_REQUEST);
//        $userAd=json_encode(array('0'=>1));
//        d($userAd);
//        d('userAd');
//      $params=[
//          'ENTITY' => 'setup',
//          'ID'=>2,
//          'NAME' => 'setupOne',
//          'PROPERTY_VALUES' => [
//              'UF_CS_ADMIN' => $userAd
//          ]
//      ];
//      $resItemAdd = $auth->CScore->call('entity.item.update', $params);
//      d($resItemAdd);
    $resSetup = $auth->CScore->call('entity.item.get', ['ENTITY' => 'setup', 'SORT' => [], 'FILTER' => []])[0];
    $adminUsers = [];
    if (!empty($resSetup['PROPERTY_VALUES']['UF_CS_ADMIN'])) {
        $adminUsers = json_decode(htmlspecialchars_decode($resSetup['PROPERTY_VALUES']['UF_CS_ADMIN']), true);
    }
    if (!empty($adminUsers))
        if (in_array($user['ID'], $adminUsers)) {
            $arResult['admin'] = "Y";
        }
    $marketingUsers = (!empty($resSetup['PROPERTY_VALUES']['UF_USERS_MARKETING'])) ? json_decode(
        htmlspecialchars_decode($resSetup['PROPERTY_VALUES']['UF_USERS_MARKETING']),
        true
    ) : [];
    $plansUsers = (!empty($resSetup['PROPERTY_VALUES']['UF_USERS_PLANS'])) ? json_decode(
        htmlspecialchars_decode($resSetup['PROPERTY_VALUES']['UF_USERS_PLANS']),
        true
    ) : [];
    if ($marketingUsers) {
        $arResult['marketing'] = (in_array($user['ID'], $marketingUsers, true) || $arResult['admin'] === 'Y') ? true : false;
    }
    if ($plansUsers) {
        $arResult['plans'] = (in_array($user['ID'], $plansUsers, true) || $arResult['admin'] === 'Y') ? true : false;
    }
    if ($_REQUEST['PLACEMENT']) {
        $arResult['PLACEMENT'] = $arParams['PLACEMENT'];
    }
    if ($arParams['templateType']) {
        $templateType = $arParams['templateType'];
    }
    if ($arParams['client_id']) {
        $arResult['client_id'] = $arParams['client_id'];
    }
    if ($arParams['resCode']) {
        $arResult['resCode'] = $arParams['resCode'];
    }
    $this->IncludeComponentTemplate($templateType);
}