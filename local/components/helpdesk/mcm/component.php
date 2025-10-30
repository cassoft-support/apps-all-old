<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $date = date("d.m.YTH:i");
    $log = __DIR__ . "/logComp.txt";
    p($arParams, 'start', $log);

$auth= new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
p($resSetup , "resSetup", $log);
//pr($resSetup, '');
    if (empty($resSetup)) {
        $paramsAdd = [
            'ENTITY' => 'setup_messager',
            'NAME' => 'setup',
        ];
        $resSetupAdd = $auth->CScore->call('entity.item.add', $paramsAdd);
        $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
    }
   // d($resSetup);
    $arResult['ID'] = $resSetup['ID'];
    $arResult['member_id'] = $arParams['member_id'];
    $arResult['app'] = $arParams['app'];
    $arResult['options'] = $arParams['~PLACEMENT_OPTIONS'];
   $arResult['PROP'] = $resSetup['PROPERTY_VALUES'];
//    $arResult['CS_CIAN_CONNECT'] = $resSetup['PROPERTY_VALUES']['CS_CIAN_CONNECT'];
$profileHb = new \CSlibs\B24\HL\HlService('profile_cassoft_wappi');
$resListProfile= $profileHb->getByFilterList(['UF_MEMBER_ID'=>$arParams['member_id']]);
foreach ($resListProfile as $profile){
    $guide[$profile['UF_PROFILE_ID']]=$profile['UF_PROFILE_NAME'];
}
$arResult['profile']=$guide;
  //   d($arResult);
    $this->IncludeComponentTemplate();