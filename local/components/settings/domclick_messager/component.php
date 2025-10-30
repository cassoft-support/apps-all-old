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
    $arResult['app'] = $arParams['app'];
    $arResult['options'] = $arParams['~PLACEMENT_OPTIONS'];
    $arResult['CS_DC_LINE'] = $resSetup['PROPERTY_VALUES']['CS_DC_LINE'];
    $arResult['CS_KEY_DC'] = $resSetup['PROPERTY_VALUES']['CS_KEY_DC'];
    $arResult['CS_DC_CONNECT'] = $resSetup['PROPERTY_VALUES']['CS_DC_CONNECT'];


  //   d($arResult);
    $this->IncludeComponentTemplate();