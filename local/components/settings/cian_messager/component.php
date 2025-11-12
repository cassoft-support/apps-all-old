<?php
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
    $date = date("d.m.YTH:i");
    $log = __DIR__ . "/logComp.txt";
    p($arParams, 'start', $log);
$line = json_decode($arParams['~PLACEMENT_OPTIONS'], true)['LINE'];
p($options , "options", $log);

$auth= new \CSlibs\B24\Auth\Auth($arParams['app'], [], $arParams['member_id']);
    $params = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [
           'PROPERTY_CS_CIAN_LINE'=>$line
        ],
    ];
    $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
  $paramsT = [
        'ENTITY' => 'setup_messager',
        'sort' => [],
        'filter' => [
          // 'PROPERTY_CS_CIAN_LINE'=>$line
        ],
    ];
    $resSetupT = $auth->CScore->call('entity.item.get', $paramsT);
p($resSetupT , "resSetupT", $log);
//pr($resSetup, '');
    if (empty($resSetup)) {
        $paramsAdd = [
            'ENTITY' => 'setup_messager',
            'NAME' => 'setup',
            'PROPERTY_VALUES'=>[
                'CS_CIAN_LINE'=>$line
            ]
        ];
        p($paramsAdd , "paramsAdd", $log);
        $resSetupAdd = $auth->CScore->call('entity.item.add', $paramsAdd);
        $resSetup = $auth->CScore->call('entity.item.get', $params)[0];
    }
p($resSetup , "resSetup", $log);
   // d($resSetup);
    $arResult['ID'] = $resSetup['ID'];
    $arResult['app'] = $arParams['app'];
    $arResult['options'] = $arParams['~PLACEMENT_OPTIONS'];
    $arResult['CS_KEY_CIAN'] = $resSetup['PROPERTY_VALUES']['CS_KEY_CIAN'];
    $arResult['CS_CIAN_CONNECT'] = $resSetup['PROPERTY_VALUES']['CS_CIAN_CONNECT'];
    $arResult['CS_CIAN_LINE'] = $resSetup['PROPERTY_VALUES']['CS_CIAN_LINE'];
$paramsCon=[
    'CONNECTOR'=> 'cs_cian_connector',
    'LINE'=>9
];
$resConnector = $auth->CScore->call('imconnector.status', $paramsCon);
p($resConnector , "resConnector", $log);

  //   d($arResult);
    $this->IncludeComponentTemplate();