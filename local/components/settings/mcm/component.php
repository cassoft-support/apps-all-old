<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
   
    $log = __DIR__ . "/logComp.txt";
    p($arParams, 'start', $log);

$auth= new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
if($arParams['~PLACEMENT_OPTIONS']){
    $arResult['options'] = $arParams['~PLACEMENT_OPTIONS'];
    $option = json_decode($arParams['~PLACEMENT_OPTIONS'], true);
    p($option , "option", $log);
}

    $arResult['member_id'] = $arParams['member_id'];
    $arResult['app'] = $arParams['app'];

    if(!empty($option["CONNECTOR"]) && !empty($option["LINE"]) && $option["REGISTER_STATUS"] == true ) {
        $profileLine =  profileLineCsMcm($arResult['member_id'], $option["LINE"], $option["CONNECTOR"]);
        p($profileLine , "profileLine", $log);
        $profileLine =  profileCsMcm($arResult['member_id']);
        p($profileLine , "profileLine", $log);

        $arResult['deactive'] = '';
        $arResult['active'] = 'display:none;';
        $arResult['profileId'] = $profileLine['UF_PROFILE_ID'];
      }else {
        $resListProfile = profileCsMcm($arResult['member_id']);

        if ($resListProfile) {
            foreach ($resListProfile as $profile) {
              p($profile , "profile", $log);
                $guide[$profile['UF_PROFILE_ID']] = $profile['UF_PROFILE_NAME'];
                $profileStatus = sendGetWappi('/api/sync/get/status?profile_id=' . $profile['UF_PROFILE_ID']);
                p($profileStatus, "profileStatus", $log);
                $profileList['name'] = $profile['UF_NAME'];
                $profileList['profile_name'] = $profile['UF_PROFILE_NAME'];
                $profileList['profile'] = $profile['UF_PROFILE_ID'];
                $profileList['type'] = $profile['UF_TYPE'];
                $profileList['date_close'] = date('d.m.Y', $profile['UF_DATE_CLOSE']);
                if (empty($profileStatus['authorized'])) {
                    $profileList['style'] = '#f30f0f';
                    $profileList['authorized'] = 'N';
                } else {
                    $profileList['style'] = 'green';
                    $profileList['authorized'] = 'Y';
                }
                $arResult['profile'][] = $profileList;
            }

            $arResult['profileSelect'] = $guide;
        }
        $arResult['deactive'] = 'display:none;';
        $arResult['active'] = '';
    }
p($arResult , "arResult", $log);
    $this->IncludeComponentTemplate();