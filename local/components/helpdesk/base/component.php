<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $date = date("d.m.YTH:i");
    $log = __DIR__ . "/logComp.txt";
    p('start', 'start', $log);
    p($arParams, 'params', $log);
   // d($arParams);
$auth= new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['auth']['member_id']);
    $params = [
        'ENTITY' => 'setup',
        'sort' => [],
        'filter' => [],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0];

    if (empty($resSetup)) {
        $paramsAdd = [
            'ENTITY' => 'setup',
            'NAME' => 'setup',
        ];
        $resSetupAdd = $auth->CScore->call('entity.item.add', $paramsAdd);
        $resSetup = $auth->CScore->call('entity.item.get', $params);
    }
   // d($resSetup);
    $arResult['ID'] = $resSetup['ID'];
    $arResult['PROP'] = $resSetup['PROPERTY_VALUES'];

    //d($arResult);
    $this->IncludeComponentTemplate();