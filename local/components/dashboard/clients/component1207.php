<?php

    $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
   // require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/CSrest.php");
//    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/lib/bitrixAuth/Auth.php");
//    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/Services/HlService.php';
//    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/debug.php';
//    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/classes/gf.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/lib/Install/tools.php';
    $debug = new \debug('debug');
    $file_log = __DIR__ . "/logComp.txt";
    file_put_contents($file_log, print_r("compDashboard\n", true));
    file_put_contents($file_log, print_r($arParams, true), FILE_APPEND);
    file_put_contents($file_log, print_r($_REQUEST, true), FILE_APPEND);

    if (!empty($arParams['member_id']) && !empty($arParams['app'])) {
        $CSRest = new  \CSRest($arParams['app']);
        $user = $CSRest->call("user.current");
        // d($user);
        //  $debug->printR($user, "adm");
        file_put_contents($file_log, print_r($user, true), FILE_APPEND);
        $memberId = $arParams['member_id'];
        $arResult["app"] = $arParams['app'];
        $arResult["deal_id"] = $arParams['deal_id'];
        $resSetup = $CSRest->call('entity.item.get', ['ENTITY' => 'setup', 'SORT' => [], 'FILTER' => []]);
        //d($resSetup);
        if ($resSetup['total'] > 0) {
            $arSetup = $resSetup['result'][0]['PROPERTY_VALUES'];
        }
        $adminUsers = (!empty($arSetup['UF_CS_ADMIN'])) ? json_decode(
            htmlspecialchars_decode($arSetup['UF_CS_ADMIN']),
            true
        ) : [];
     
        if (in_array($user['result']['ID'], $adminUsers)) {
            $arResult['admin'] = "Y";
        }

//echo $arParams['arB24App'];
        $arResult['UserAut'] = json_encode($_REQUEST);

        // $code = json_encode($arParams['settings']);
        $userId = $arParams['user']['ID'];

        //  $decode = json_decode($code, true);


        $marketingUsers = (!empty($arParams['brokciParams']['UF_USERS_MARKETING'])) ? json_decode(
            htmlspecialchars_decode($arParams['brokciParams']['UF_USERS_MARKETING']),
            true
        ) : [];
        $plansUsers = (!empty($arParams['brokciParams']['UF_USERS_PLANS'])) ? json_decode(
            htmlspecialchars_decode($arParams['brokciParams']['UF_USERS_PLANS']),
            true
        ) : [];
        if ($marketingUsers) {
            $arResult['marketing'] = (in_array($userId, $marketingUsers, true) || $arParams['isAdmin']) ? true : false;
        }
        if ($plansUsers) {
            $arResult['plans'] = (in_array($userId, $plansUsers, true) || $arParams['isAdmin']) ? true : false;
        }

        if ($arParams['isAdmin']) {
            $arResult['admin'] = $arParams['isAdmin'];
        }

        $arResult['country'] = $arParams['brokciParams']['UF_CS_COUNTRY_ID'];
        $arResult['region'] = $arParams['brokciParams']['UF_CS_REGION_ID'];
        $arResult['city'] = $arParams['brokciParams']['UF_CS_CITY_ID'];
        $arResult['member_id'] = $arParams['member_id'];
        $arResult['user_id'] = $arParams['user']['ID'];
        if ($arParams['user']['LAST_NAME']) {
            $arResult['userFIO'] = $arParams['user']['LAST_NAME'];
        }
        if ($user) {
            $arResult['userFIO'] = $user['result']['LAST_NAME'];
            $arResult['user_id'] = $user['result']['ID'];
        }

        $arResult['domain'] = $_REQUEST['DOMAIN'];

//echo "<pre>"; print_r($marketingUsers); echo "</pre>";
//echo "<pre>"; print_r($arResult); echo "</pre>";
    }
    if($_REQUEST['PLACEMENT']){
        $arResult['PLACEMENT'] = $arParams['PLACEMENT'];
    }
    if($arParams['templateType']) {
        $templateType = $arParams['templateType'];
    }
    if($arParams['client_id']) {
        $arResult['client_id'] = $arParams['client_id'];
    }
    if($arParams['resCode']) {
        $arResult['resCode'] = $arParams['resCode'];
    }
    $this->IncludeComponentTemplate($templateType);
