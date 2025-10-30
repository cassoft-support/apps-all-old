<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die();
    }
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $log = __DIR__ . "/logComp.txt";
    p($arParams, 'start', $log);
$auth= new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
    $params = [
        'ENTITY' => 'setup',
        'sort' => [],
        'filter' => [],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
p($resSetup , "resSetup", $log);
//pr($resSetup, '');
    if (empty($resSetup)) {
        $paramsAdd = [
            'ENTITY' => 'setup',
            'NAME' => 'setup',
        ];
        $resSetupAdd = $auth->CScore->call('entity.item.add', $paramsAdd);
        $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
    }
   // d($resSetup);
    $arResult['ID'] = $resSetup['ID'];
    $arResult['app'] = $arParams['app'];
    $arResult['options'] = $arParams['~PLACEMENT_OPTIONS'];
    $arResult['PROP'] = $resSetup['PROPERTY_VALUES'];

    $this->IncludeComponentTemplate();